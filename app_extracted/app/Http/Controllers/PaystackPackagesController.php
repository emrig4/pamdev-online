<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaystackPackagesController extends Controller
{
    private $paystackSecretKey;
    private $paystackPublicKey;
    private $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->paystackSecretKey = config('services.paystack.secret');
        $this->paystackPublicKey = config('services.paystack.public');
    }

    public function index()
    {
        $packages = [
            [
                'id' => 'basic',
                'name' => 'BASIC',
                'ranc_amount' => 5000,
                'price' => 5000,
                'description' => '5,000 RANC Credits',
                'popular' => false
            ],
            [
                'id' => 'standard', 
                'name' => 'STANDARD',
                'ranc_amount' => 10000,
                'price' => 10000,
                'description' => '10,000 RANC Credits',
                'popular' => true
            ],
            [
                'id' => 'pro',
                'name' => 'PRO',
                'ranc_amount' => 20000,
                'price' => 20000,
                'description' => '20,000 RANC Credits',
                'popular' => false
            ]
        ];

        return view('packages', compact('packages'));
    }

    public function initializePayment(Request $request)
    {
        $request->validate([
            'package_id' => 'required|in:basic,standard,pro',
            'email' => 'required|email'
        ]);

        $package = $this->getPackageDetails($request->package_id);
        if (!$package) {
            return back()->with('error', 'Invalid package selected.');
        }

        $user = $this->getOrCreateUser($request->email);
        $reference = 'RC-' . Str::uuid()->toString();
        
        session([
            'pending_payment' => [
                'reference' => $reference,
                'user_id' => $user->id,
                'package_id' => $request->package_id,
                'ranc_amount' => $package['ranc_amount'],
                'amount' => $package['price'] * 100
            ]
        ]);

        $paymentData = [
            'email' => $request->email,
            'amount' => $package['price'] * 100,
            'currency' => 'NGN',
            'reference' => $reference,
            'metadata' => [
                'user_id' => $user->id,
                'package_id' => $request->package_id,
                'ranc_amount' => $package['ranc_amount']
            ],
            'callback_url' => route('paystack.callback')
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->paystackSecretKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/transaction/initialize', $paymentData);

            $data = $response->json();

            if ($data['status'] ?? false) {
                return redirect($data['data']['authorization_url']);
            } else {
                return back()->with('error', 'Payment initialization failed: ' . ($data['message'] ?? 
'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Paystack payment initialization error: ' . $e->getMessage());
            return back()->with('error', 'Payment initialization failed. Please try again.');
        }
    }

    public function paymentCallback(Request $request)
    {
        $reference = $request->query('reference');
        
        if (!$reference) {
            return redirect()->route('packages.index')->with('error', 'Invalid payment reference.');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->paystackSecretKey
            ])->get($this->baseUrl . '/transaction/verify/' . $reference);

            $data = $response->json();

            if ($data['status'] ?? false && $data['data']['status'] === 'success') {
                $this->creditUserWallet($data['data']);
                return redirect()->route('packages.success')->with('success', 'Payment successful! 
Your wallet has been credited.');
            } else {
                return redirect()->route('packages.index')->with('error', 'Payment verification 
failed.');
            }
        } catch (\Exception $e) {
            Log::error('Paystack payment verification error: ' . $e->getMessage());
            return redirect()->route('packages.index')->with('error', 'Payment verification failed. 
Please contact support.');
        }
    }

    public function webhook(Request $request)
    {
        $signature = $request->header('x-paystack-signature');
        
        if (!$this->verifyWebhookSignature($request->getContent(), $signature)) {
            return response('Unauthorized', 401);
        }

        $event = json_decode($request->getContent(), true);

        if ($event['event'] === 'charge.success') {
            $data = $event['data'];
            
            if ($data['status'] === 'success') {
                $this->creditUserWallet($data);
            }
        }

        return response('OK', 200);
    }

    private function creditUserWallet($paymentData)
    {
        try {
            $userId = $paymentData['metadata']['user_id'];
            $rancAmount = $paymentData['metadata']['ranc_amount'];
            $reference = $paymentData['reference'];

            DB::table('subscription_wallets')->where('user_id', $userId)->increment('ranc', 
$rancAmount);

            $wallet = DB::table('subscription_wallets')->where('user_id', $userId)->first();
            
            DB::table('wallet_transactions')->insert([
                'user_id' => $userId,
                'amount' => $rancAmount,
                'type' => 'credit',
                'balance_after' => $wallet->ranc,
                'reference' => $reference,
                'description' => 'Paystack Package Purchase - ' . 
ucfirst($paymentData['metadata']['package_id']),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            session()->forget('pending_payment');

            Log::info("Wallet credited successfully for user {$userId}: {$rancAmount} RANC");

        } catch (\Exception $e) {
            Log::error('Error crediting wallet: ' . $e->getMessage());
        }
    }

    private function getPackageDetails($packageId)
    {
        $packages = [
            'basic' => ['ranc_amount' => 5000, 'price' => 5000],
            'standard' => ['ranc_amount' => 10000, 'price' => 10000],
            'pro' => ['ranc_amount' => 20000, 'price' => 20000]
        ];

        return $packages[$packageId] ?? null;
    }

    private function getOrCreateUser($email)
    {
        $user = \App\Models\User::where('email', $email)->first();
        
        if (!$user) {
            $user = \App\Models\User::create([
                'email' => $email,
                'first_name' => 'Paystack',
                'last_name' => 'User',
                'username' => 'user_' . Str::random(8),
                'email_verified_at' => now(),
                'password' => bcrypt(Str::random(16))
            ]);

            DB::table('subscription_wallets')->insert([
                'user_id' => $user->id,
                'ranc' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return $user;
    }

    private function verifyWebhookSignature($payload, $signature)
    {
        $hash = hash_hmac('sha512', $payload, $this->paystackSecretKey);
        return hash_equals($signature, $hash);
    }

    public function success()
    {
        return view('packages-success');
    }
}

