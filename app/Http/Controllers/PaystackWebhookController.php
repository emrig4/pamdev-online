<?php

namespace App\Http\Controllers;

use App\Modules\Wallet\Http\Traits\WalletTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Subscription\Models\PaystackSubscription;

class PaystackWebhookController extends Controller
{
    use WalletTrait;

    public function handleWebhook(Request $request)
    {
        $secretKey = config('services.paystack.secret');

        // Verify Paystack signature
        $signature = $request->header('x-paystack-signature');
        $payload = $request->getContent();
        
        $expectedSignature = hash_hmac('sha512', $payload, $secretKey);
        
        if (!hash_equals($signature, $expectedSignature)) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = json_decode($payload, true);
        $event = $data['event'];

        // Handle subscription creation event
        if ($event === 'subscription.create') {
            $this->handleSubscriptionCreate($data);
        }

        // Handle subscription disable event
        if ($event === 'subscription.disable') {
            $this->handleSubscriptionDisable($data);
        }

        return response()->json(['status' => 'success']);
    }

    private function handleSubscriptionCreate($data)
    {
        $subscriptionData = $data['data'];
        $userEmail = $subscriptionData['customer']['email'];
        
        $user = User::where('email', $userEmail)->first();
        
        if (!$user) {
            \Log::error('User not found for email: ' . $userEmail);
            return;
        }

        // Convert fiat amount to RANC
        $fiat_amount = $subscriptionData['amount'] / 100; // Paystack sends in kobo/cents
        $fiat_currency = $subscriptionData['plan']['currency'];
        $ranc_amount = ranc_equivalent($fiat_amount, $fiat_currency);

        // Credit subscription wallet
        self::creditSubscriptionWallet($ranc_amount, $user->id);

        \Log::info("User {$user->email} subscribed. RANC credited: {$ranc_amount}");
    }

    private function handleSubscriptionDisable($data)
    {
        $subscriptionData = $data['data'];
        $subscriptionCode = $subscriptionData['subscription_code'];
        
        $subscription = PaystackSubscription::where('paystack_subscription_code', $subscriptionCode)->first();
        
        if ($subscription) {
            $subscription->update(['paystack_status' => 'disabled', 'status' => 'inactive']);
            \Log::info("Subscription {$subscriptionCode} disabled");
        }
    }
}