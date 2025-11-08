<?php

namespace App\Modules\Subscription\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use Digikraaft\Paystack\Plan;


// use Digikraaft\PaystackWebhooks\Http\Controllers\WebhooksController as PaystackWebhooksController;
use App\Modules\Subscription\Events\PaystackWebhookEvent;

use Digikraaft\Paystack\Paystack;
use Spatie\SlackAlerts\Facades\SlackAlert;
use Digikraaft\PaystackSubscription\Payment;
use App\Modules\Subscription\Http\Traits\SubscriptionTrait;
use Digikraaft\Paystack\Subscription as PaystackSubscription;
use App\Modules\Wallet\Http\Traits\WalletTrait;

use App\Modules\Payment\Models\Payment as AuthoranPayment;



class SubscriptionController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
        Paystack::setApiKey(config('paystacksubscription.secret', env('PAYSTACK_SECRET')));
    }

    /**
     * Handle WebhookEvent.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleWebhookEvent(){}

    /**
     *
     * @throws \Digikraaft\PaystackSubscription\Exceptions\PaymentFailure
     */
    public function verifyPaystack(Request $request)
    {
        
        // get transaction reference returned by Paystack
        $transactionRef = $request->reference;


        //verify the transaction is valid
        $transaction = Payment::hasValidTransaction($transactionRef);

        if ($transaction) {

            // \Log::channel('slack')->info('subscription activated ' , [$transaction] );
            return $this->handleChargeSuccess($transaction);
        }
        throw PaymentFailure::incompleteTransaction($transaction);
    }

    /**
     * Handle charge success.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleChargeSuccess($payload)
    {

        // store payment
        $transaction = $this->storePayment($payload->data);


        if($payload->data->metadata->txntype == 'buycredit'){
            $base_amount = $transaction->amount;
            $fiat_currency = $transaction->currency;
            $fiat_amount = $base_amount/100;
            $ranc_amount = ranc_equivalent($fiat_amount, $fiat_currency);
            
            WalletTrait::creditSubscriptionWallet($ranc_amount); 
            $response = $transaction; 

        } else{

            // create subscription on paystack
            $this->createSubscription($payload->data);
            
            // store subscription
            $subscription = $this->storeSubscription($payload->data);
            $response = $subscription;

        }
        


        if (request()->header('Content-Type') == 'application/json' ) {
            return response()->json(['status'=> 'success', 'data' => $response], 201);
        } 

       return redirect()->back();
    }


    /** show view that displays available subscriptions */
    public function showBillingPlans()
    {
        Paystack::setApiKey(env('PAYSTACK_SECRET'));
        $plans = Plan::list()->data;

        return view('billing_plans', compact('plans'));
    }


    /**
     * Create Subscription for user.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createSubscription($payload)
    {
       return SubscriptionTrait::createSubscription($payload);
    }


    /**
     * Store a subscription on local db
     */
    public function storeSubscription($payload)
    {
        $plan = Plan::fetch($payload->metadata->plan_id)->data;
        $customer = $payload->customer;
        return SubscriptionTrait::storeSubscription($plan, $customer);
    }


    /**
     * Store Payment.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function storePayment($payload)
    {
        // Store payment
        $paymentMeta = $payload->metadata;
        $verifiedChargeAmount = $payload->amount;
        $verifiedChargeCurrency = $payload->currency;
        $userId = $payload->metadata->user_id;

        $paymentData = [
          'status' => $payload->status,
          'reference' => $payload->reference,
          'txntype' => $paymentMeta->txntype ?? 'payment', //subscription, downloads, purchase
          'paid_at' => $payload->paid_at ?? 'payment', //subscription, downloads
          'gateway' => 'paystack',
          'channel' => 'online',
          'amount' => $verifiedChargeAmount,
          'currency' => $verifiedChargeCurrency,
          'meta' => json_encode($paymentMeta),
          'user_id' => $userId
        ];

        return AuthoranPayment::updateOrCreate(
            ['reference' => $paymentData['reference'] ],
            $paymentData
        );
    }


    /**
     * Credit User Subscription wallet.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createSubscriptionWallet($subscription)
    {
        return SubscriptionTrait::createSubscriptionWallet($payload);
    }



    /**
     * Cancel the subscription at the end of the current billing period
     */
    public function cancelSubscription()
    {   
        $subscription =  request()->subscription;

        // dd( auth()->user()
        //     ->subscription() );

       try {
        
         auth()->user()
            ->subscription($subscription)
            ->cancel() ;

        return redirect()->back();
           
       } catch (GuzzleHttp\Exception\ClientException $e) {
            dd($e);
       }    
    }//

    /**
     * Restart a canceled subscription
     */
    public function restartSubscription()
    {

        $subscription =  request()->subscription;
        try {
        
         auth()->user()
            ->subscription($subscription)
            ->enable() ;

        return redirect()->back();
           
       } catch (GuzzleHttp\Exception\ClientException $e) {
            dd($e);
       }    
    }


    /**
     * Refresh a canceled subscription
     */
    public function refreshSubscriptions()
    {
        $subscriptions = PaystackSubscription::list(['email' => auth()->user()->email ])->data;
        SubscriptionTrait::syncSubscriptions($subscriptions);
        return redirect()->back();
    }
}



// {
//   "id": 1719632977,
//   "domain": "test",
//   "status": "success",
//   "reference": "e0961976-d837-4702-baee-6e1f04dae41f",
//   "amount": 780820,
//   "message": null,
//   "gateway_response": "Successful",
//   "paid_at": "2022-03-31T07:14:38.000Z",
//   "created_at": "2022-03-31T07:14:28.000Z",
//   "channel": "card",
//   "currency": "NGN",
//   "ip_address": "197.210.52.5",
//   "metadata": {
//     "referrer": "http://authoran.test/pricings/basic"
//   },
//   "log": {
//     "start_time": 1648710822,
//     "time_spent": 7,
//     "attempts": 1,
//     "errors": 0,
//     "success": true,
//     "mobile": false,
//     "input": {},
//     "history": {
//       "0": {
//         "type": "action",
//         "message": "Attempted to pay with card",
//         "time": 7
//       },
//       "1": {
//         "type": "success",
//         "message": "Successfully paid with card",
//         "time": 7
//       }
//     }
//   },
//   "fees": 21713,
//   "fees_split": null,
//   "authorization": {
//     "authorization_code": "AUTH_2qe70qoqtr",
//     "bin": "408408",
//     "last4": "4081",
//     "exp_month": "12",
//     "exp_year": "2030",
//     "channel": "card",
//     "card_type": "visa ",
//     "bank": "TEST BANK",
//     "country_code": "NG",
//     "brand": "visa",
//     "reusable": true,
//     "signature": "SIG_XUuKb1r5MZaoscUey6Ra",
//     "account_name": null,
//     "receiver_bank_account_number": null,
//     "receiver_bank": null
//   },
//   "customer": {
//     "id": 51135079,
//     "first_name": "Aaron Dev",
//     "last_name": "Aaron",
//     "email": "aironde.v@gmail.com",
//     "customer_code": "CUS_tsywaoe37b432d2",
//     "phone": "",
//     "metadata": null,
//     "risk_action": "default",
//     "international_format_phone": null
//   },
//   "plan": null,
//   "split": {},
//   "order_id": null,
//   "paidAt": "2022-03-31T07:14:38.000Z",
//   "createdAt": "2022-03-31T07:14:28.000Z",
//   "requested_amount": 780820,
//   "pos_transaction_data": null,
//   "source": null,
//   "fees_breakdown": null,
//   "transaction_date": "2022-03-31T07:14:28.000Z",
//   "plan_object": {},
//   "subaccount": {}
// }




// Subscription Object
// {
//     "customer": 22179939,
//     "plan": 209914,
//     "integration": 149366,
//     "domain": "test",
//     "start": 1648762438,
//     "status": "active",
//     "quantity": 1,
//     "amount": 200000,
//     "authorization": 309146570,
//     "invoice_limit": 0,
//     "split_code": null,
//     "subscription_code": "SUB_32i5c8av8mmw56w",
//     "email_token": "9vv7avedpwir6im",
//     "id": 387561,
//     "cancelledAt": null,
//     "createdAt": "2022-03-31T21:33:58.052Z",
//     "updatedAt": "2022-03-31T21:33:58.052Z",
//     "cron_expression": "0 * * * *",
//     "next_payment_date": "2022-03-31T22:00:00.000Z"
// }