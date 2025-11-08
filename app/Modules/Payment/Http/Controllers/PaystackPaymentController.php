<?php

namespace App\Modules\Payment\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Paystack;
use Illuminate\Support\Facades\DB;

class PaystackPaymentController extends Controller
{
    /**
   * Redirect the User to Paystack Payment Page
   * @return Url
   */
    public function redirectToGateway()
    {
        try{
            return Paystack::getAuthorizationUrl()->redirectNow();
        }catch(\Exception $e) {
            // return redirect()->back()->withMessage(['msg'=>'The paystack token has expired. Please refresh the page and try again.', 'type'=>'error']);
            dd($e);
        }        
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
      $paymentDetails = Paystack::getPaymentData();
      if($paymentDetails['status'] == 'true'){
        $paymentData = $paymentDetails['data'];
        $paymentStatus = $paymentDetails['status'];
        $paymentMeta = $paymentData['metadata'];
        $verifiedChargeAmount = $paymentData['amount'];
        $verifiedChargeCurrency = $paymentData['currency'];


     $txMeta = $paymentMeta; // Direct assignment
     $purchasedEbookData = [
    'ebook_id' => $txMeta['ebook_id'] ?? null, // Optional for non-ebook payments
    'customer_id' => $txMeta['user_id'] ?? '', // Use user_id as customer_id
    'transaction_id' => $paymentData['reference'] ?? '', // Use payment reference
    'is_delivered' => 0,
];

// Handle wallet credit payments
if (isset($txMeta['ranc_amount']) && isset($txMeta['user_id'])) {
    // This is a wallet credit payment (not ebook)
    $userId = $txMeta['user_id'];
    $rancAmount = $txMeta['ranc_amount'];
    
    // Ensure wallet exists, create if needed
$walletExists = DB::table('subscription_wallets')->where('user_id', $userId)->exists();
            if (!$walletExists) {
                DB::table('subscription_wallets')->insert([
                    'user_id' => $userId,
                    'ranc' => 0,
                    'reference' => 'WALLET-' . $userId . '-' . time(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            // Credit user's wallet
            DB::table('subscription_wallets')->where('user_id', $userId)->increment('ranc', $rancAmount);
            
            // Get updated wallet balance
            $wallet = DB::table('subscription_wallets')->where('user_id', $userId)->first();
// Now $wallet will never be null
    
    // Log the transaction
   DB::table('wallet_transactions')->insert([
    'user_id' => $userId,
    'amount' => $rancAmount,
    'type' => 'credit',
    'balance_after' => $wallet->ranc,
    'reference' => $paymentData['reference'],
    'description' => 'Paystack Package Purchase - ' . ucfirst($txMeta['package_id'] ?? 'Package'),
    'created_at' => now(),
    'updated_at' => now(),
]);

// ✅ Immediately redirect after successful wallet credit
return redirect()->route('packages.success')->with('success', 'Payment successful! Your wallet has been credited.');

}

        $userId = isset($txMeta['user_id']) ? $txMeta['user_id'] : null;

        $customerData = [
          'customer_type' => $txMeta['customer_type'],
          'user_id' => $userId,
          'paystack_account_id' =>$paymentData['customer']['id'],
          'phone' => $paymentData['customer']['phone'],
          'email' => $paymentData['customer']['email'],
          'name' =>  $paymentData['customer']['first_name'],
        ];

        $transactionData = [
          'status' => $paymentStatus,
          'reference_id' => $paymentData['reference'],
          'payment_type' => '',
          'payment_aggregator' => 'paystack',
          'amount' => ($paymentData['amount']/100),
          'transaction_payload' => json_encode($paymentDetails),
          'transaction_meta' => json_encode($txMeta),
          'customer_id' => '', // references local instance
        ];

       
        $customer = BookPurchasedTrait::storeCustomer($customerData);
        $transactionData['customer_id'] = $customer->id;
        $transaction = BookPurchasedTrait::storeTransaction($transactionData);

        $purchasedEbookData['transaction_id'] = $transaction->id;
        $purchasedEbookData['customer_id'] = $customer->id;
        $purchasedEbook = BookPurchasedTrait::storePurchasedEbook($purchasedEbookData);

        // current customer info  to session
        request()->session()->put('customer', $customerData);

        // send id of purchased ebook to successful page, 
        // return redirect()->route('ebooks.purchased', ['id'=> id_encode($txMeta['ebook_id']) ]); //->with($custmerData);
        return redirect()->action([\Modules\Ebook\Http\Controllers\EbookController::class, 'deliverFile'], [ 'id'=>id_encode($txMeta['ebook_id']) ]);
    

        dd($paymentDetails);
        // Now you have the payment details,
        // you can store the authorization_code in your db to allow for recurrent subscriptions
        // you can then redirect or do whatever you want
      } else{
        return 'An error occured';
      }
    }



    /**
     * Show page to finalize payment.
     * @param int $id
     * @return Renderable
     */
    public function show($slug)
    {
        //
    }
}