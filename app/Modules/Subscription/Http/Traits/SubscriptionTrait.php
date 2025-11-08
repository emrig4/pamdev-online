<?php

namespace App\Modules\Subscription\Http\Traits;

use App\Models\User;
use App\Modules\Subscription\Models\PaystackSubscription as AuthoranSubscription;
use Digikraaft\PaystackWebhooks\Http\Controllers\WebhooksController as PaystackWebhooksController;
use Digikraaft\Paystack\Paystack;
use Digikraaft\Paystack\Plan;
use Digikraaft\PaystackSubscription\Exceptions\PaymentFailure;
use Digikraaft\PaystackSubscription\Payment;
use Digikraaft\PaystackSubscription\Exceptions\SubscriptionUpdateFailure;
use Illuminate\Support\Carbon;
use Digikraaft\Paystack\Subscription as PaystackSubscription;

use App\Modules\Wallet\Http\Traits\WalletTrait;

trait SubscriptionTrait
{


    
    /**
     * Create Subscription for user.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
    */
    public static function createSubscription($payload)
    {
    
        //check if user is already subscribed to plan. This is to guard against multiple subscriptions with the same transactionId

        $plan = Plan::fetch($payload->metadata->plan_id)->data;

        $user = User::find($payload->metadata->user_id);
        $isSubscribed = $user->subscribedToPlan($plan->plan_code);
        
        // $plan = Plan::fetch('PLN_eohnjyhr4zvlyyd');
        $plan_id = $payload->metadata->plan_id;
        $authorization = $payload->authorization->authorization_code;

        $payload = array_merge(
            ['customer' => $payload->customer->customer_code],
            ['authorization' => $authorization],
            ['plan' => $plan_id],
        );

       
        try {
            return PaystackSubscription::create($payload)->data;
        } catch ( \GuzzleHttp\Exception\ClientException $e) {
            //throw SubscriptionUpdateFailure::duplicateSubscription($user, 209914);
           return json_encode(['status' => 'error', 'message' => 'User is already subscribed']);
        }
    }


    /**
     * Store a subscription on local db
    */
    public static function storeSubscription($plan, $customer)
    {
        $user = User::whereEmail($customer->email)->first();        
        $paystackSubscription = \Digikraaft\Paystack\Subscription::list(
            [
                'perPage' => 1,
                'page' => 1,
                'customer' => $customer->id,
                'plan' => $plan->id,
            ]
        )->data->{0};

        // ensure subscription has not been added to user already
        if($paystackSubscription){

            $params = [
                    'name' => $plan->name,
                    'subscription_code' => $paystackSubscription->subscription_code,
                    'subscription_id' => $paystackSubscription->id,
                    'paystack_status' => $paystackSubscription->status,
                    'paystack_plan' => json_encode($paystackSubscription->plan),
                    'quantity' => $paystackSubscription->quantity ?? '1',
                    'email_token' => $paystackSubscription->email_token,
                    'authorization' => json_encode($paystackSubscription->authorization),
                    // 'meta' => json_encode($payload->metadata),
                    'next_payment_date' => Carbon::create( $paystackSubscription->next_payment_date  ),
                    'user_id' => $user->id,
                    'customer' => json_encode($paystackSubscription->customer),
                    'most_recent_invoice' => json_encode($paystackSubscription->most_recent_invoice),
                    'payments_count' =>  $paystackSubscription->payments_count,
                    'paystack_id' => $user->paystack_id,
                    'updated_at' => now(),
                ];

            $subscription = AuthoranSubscription::where('subscription_id', $paystackSubscription->id )->first();

            $base_amount = $paystackSubscription->plan->amount;
            $fiat_currency = $paystackSubscription->plan->currency;
            $fiat_amount = $base_amount/100;
            $ranc_amount = ranc_equivalent($fiat_amount, $fiat_currency);


            \Log::channel('slack')->info('subscription payment processed ' , ['fiat amount' => $fiat_amount,  'ranc amount' => $ranc_amount] );
           
            if($subscription){
                $counts = $paystackSubscription->payments_count - $subscription->payments_count;
                $subscription->update($params);
                WalletTrait::creditSubscriptionWallet($ranc_amount * $counts, $user->id );
            }else{

                $counts = 1;
                $subscription = AuthoranSubscription::create($params);
                WalletTrait::creditSubscriptionWallet($ranc_amount,  $user->id);
            }
            return $subscription;
        }
    }


    /**
     * Store a subscription on local db
    */
    public static function syncSubscriptions($payload)
    {

        foreach($payload as $subscription){
            $user = User::whereEmail($subscription->customer->email)->first();
            $params = [

                'name' => $subscription->plan->name,
                'subscription_code' => $subscription->subscription_code,
                'subscription_id' => $subscription->id,
                'paystack_status' => $subscription->status,
                'paystack_plan' => json_encode($subscription->plan),
                'quantity' => $subscription->quantity ?? '1',
                'email_token' => $subscription->email_token,
                'authorization' => json_encode($subscription->authorization),
                // 'meta' => json_encode($payload->metadata),
                'next_payment_date' => Carbon::create( $subscription->next_payment_date  ),
                'user_id' => $user->id,
                'paystack_id' => $user->paystack_id,
                'customer' => json_encode($subscription->customer),
                'most_recent_invoice' => json_encode($subscription->most_recent_invoice),
                'payments_count' =>  $subscription->payments_count,
                'updated_at' => now(),
               
            ];

            // dd($subscription->plan);

            $sub = AuthoranSubscription::where('subscription_id', $subscription->id )->first();

            $base_amount = $subscription->amount;
            $fiat_currency = $subscription->plan->currency;
            $fiat_amount = $base_amount/100;
            $ranc_amount = ranc_equivalent($fiat_amount, $fiat_currency);

            if($sub){
                $counts = $subscription->payments_count - $sub->payments_count;
                $sub->update($params);
                WalletTrait::creditSubscriptionWallet($ranc_amount * $counts, $user->id );
            }else{
                $counts = 1;
                AuthoranSubscription::create($params);
                WalletTrait::creditSubscriptionWallet($ranc_amount,  $user->id);
            }



        }
    }


    /**
     * Credit User Subscription wallet.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
    */
    public static function createSubscriptionWallet($subscription)
    {

        $userId = $payload->metadata->user_id;
        $subscriptionData = [
          'paystack_subscription_id' => $payload->reference,
          'expiring' => 'subscription', //subscription, downloads
          'ranc' => 'paystack',
          'active' => 'online',
          'user_id' => $userId
        ];

        SubscriptionWallet::updateOrCreate(
            ['paystack_subscription_id' => $subscriptionData['reference'] ],
            $subscriptionData
        );
    }

    
}



/*

{#1824
  +"id": 390962
  +"domain": "test"
  +"status": "active"
  +"start": 1649313445
  +"quantity": 1
  +"subscription_code": "SUB_mg5sganiw06urki"
  +"email_token": "um2p8f60hg30cb9"
  +"amount": 200000
  +"cron_expression": "0 * * * *"
  +"next_payment_date": "2022-04-10T08:00:01.000Z"
  +"open_invoice": null
  +"createdAt": "2022-04-07T06:37:25.000Z"
  +"integration": 149366
  +"plan": {#1833
    +"id": 209914
    +"domain": "test"
    +"name": "BASIC"
    +"plan_code": "PLN_eohnjyhr4zvlyyd"
    +"description": "Authoran Basic Plan - Upto 10 Reads5 Access to Downloads20 Plagiarism"
    +"amount": 200000
    +"interval": "hourly"
    +"send_invoices": true
    +"send_sms": true
    +"currency": "NGN"
    +"integration": 149366
    +"createdAt": "2021-12-29T15:51:32.000Z"
    +"updatedAt": "2021-12-29T15:51:32.000Z"
  }
  +"authorization": {#1762
    +"authorization_code": "AUTH_gqxnxc70fi"
    +"bin": "408408"
    +"last4": "4081"
    +"exp_month": "12"
    +"exp_year": "2030"
    +"channel": "card"
    +"card_type": "visa "
    +"bank": "TEST BANK"
    +"country_code": "NG"
    +"brand": "visa"
    +"reusable": 1
    +"signature": "SIG_XUuKb1r5MZaoscUey6Ra"
    +"account_name": null
  }
  +"customer": {#1830
    +"id": 22179939
    +"first_name": "Aniebiet"
    +"last_name": "Aaron"
    +"email": "airondev@gmail.com"
    +"customer_code": "CUS_1pb4w230algy350"
    +"phone": ""
    +"metadata": null
    +"risk_action": "default"
    +"international_format_phone": null
  }
  +"invoice_limit": 0
  +"split_code": null
  +"payments_count": 73
  +"most_recent_invoice": {#1753
    +"subscription": 390962
    +"integration": 149366
    +"domain": "test"
    +"invoice_code": "INV_qhv4lhonv4yu754"
    +"customer": 22179939
    +"transaction": 1741442474
    +"amount": 200000
    +"period_start": "2022-04-10T07:00:00.000Z"
    +"period_end": "2022-04-10T07:59:59.000Z"
    +"status": "success"
    +"paid": 1
    +"retries": 1
    +"authorization": 312342615
    +"paid_at": "2022-04-10T07:00:05.000Z"
    +"next_notification": "2022-04-07T07:59:59.000Z"
    +"notification_flag": null
    +"description": null
    +"id": 4535629
    +"created_at": "2022-04-10T07:00:04.000Z"
    +"updated_at": "2022-04-10T07:00:06.000Z"
  }
}


*/