<?php

namespace App\Modules\Subscription\Listeners;

use App\Modules\Payment\Events\PaystackWebhookEvent;
use App\Modules\Wallet\Http\Traits\WalletTrait;
use App\Modules\Subscription\Models\Subscription;
use App\Modules\Wallet\Models\SubscriptionWallet;
use App\Modules\Wallet\Models\CreditWallet;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaystackWebhookListener
{
    use WalletTrait;

    /**
     * Handle the Paystack webhook event
     */
    public function handle(PaystackWebhookEvent $event)
    {
        $data = $event->data;
        
        Log::info('Processing Paystack webhook', [
            'event' => $event->event,
            'data' => $data
        ]);

        try {
            // Handle different webhook events
            switch ($event->event) {
                case 'subscription.create':
                    $this->handleSubscriptionCreate($data);
                    break;
                    
                case 'invoice.payment_failed':
                    $this->handlePaymentFailed($data);
                    break;
                    
                case 'subscription.disable':
                    $this->handleSubscriptionDisable($data);
                    break;
                    
                case 'charge.success':
                    $this->handleChargeSuccess($data);
                    break;
                    
                default:
                    Log::info('Unhandled Paystack webhook event', ['event' => $event->event]);
            }
            
        } catch (\Exception $e) {
            Log::error('Paystack webhook processing failed', [
                'event' => $event->event,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
        }
    }

    /**
     * Handle subscription creation
     */
    private function handleSubscriptionCreate($data)
    {
        if (isset($data['data']['subscription_code'])) {
            $subscriptionCode = $data['data']['subscription_code'];
            
            // Find the subscription by code
            $subscription = Subscription::where('paystack_subscription_code', $subscriptionCode)->first();
            
            if ($subscription) {
                // Get the user
                $user = $subscription->user;
                
                if ($user && isset($data['data']['amount'])) {
                    // Calculate RANC amount from fiat amount
                    $fiatAmount = $data['data']['amount'] / 100; // Convert from kobo to naira
                    $fiatCurrency = 'NGN';
                    
                    $rancAmount = function_exists('ranc_equivalent') 
                        ? ranc_equivalent($fiatAmount, $fiatCurrency) 
                        : $fiatAmount; // Fallback if helper doesn't exist
                    
                    // Credit the wallet
                    if ($rancAmount > 0) {
                        DB::transaction(function() use ($user, $rancAmount, $subscriptionCode) {
                            // Update subscription status
                            Subscription::where('paystack_subscription_code', $subscriptionCode)
                                ->update(['status' => 'active']);
                            
                            // Credit wallet
                            $this->creditSubscriptionWallet($rancAmount, $user->id, 'subscription');
                            
                            Log::info('Wallet credited for subscription creation', [
                                'user_id' => $user->id,
                                'subscription_code' => $subscriptionCode,
                                'ranc_amount' => $rancAmount
                            ]);
                        });
                    }
                }
            }
        }
    }

    /**
     * Handle payment failed
     */
    private function handlePaymentFailed($data)
    {
        if (isset($data['data']['subscription_code'])) {
            $subscriptionCode = $data['data']['subscription_code'];
            
            // Update subscription status
            Subscription::where('paystack_subscription_code', $subscriptionCode)
                ->update(['status' => 'payment_failed']);
                
            Log::info('Subscription payment failed', ['subscription_code' => $subscriptionCode]);
        }
    }

    /**
     * Handle subscription disable
     */
    private function handleSubscriptionDisable($data)
    {
        if (isset($data['data']['subscription_code'])) {
            $subscriptionCode = $data['data']['subscription_code'];
            
            // Update subscription status
            Subscription::where('paystack_subscription_code', $subscriptionCode)
                ->update(['status' => 'disabled']);
                
            Log::info('Subscription disabled', ['subscription_code' => $subscriptionCode]);
        }
    }

    /**
     * Handle charge success (for recurring payments)
     */
    private function handleChargeSuccess($data)
    {
        // Check if this is a subscription payment
        if (isset($data['data']['subscription']['subscription_code'])) {
            $subscriptionCode = $data['data']['subscription']['subscription_code'];
            
            $subscription = Subscription::where('paystack_subscription_code', $subscriptionCode)->first();
            
            if ($subscription) {
                $user = $subscription->user;
                
                if ($user && isset($data['data']['amount'])) {
                    $fiatAmount = $data['data']['amount'] / 100; // Convert from kobo to naira
                    $fiatCurrency = 'NGN';
                    
                    $rancAmount = function_exists('ranc_equivalent') 
                        ? ranc_equivalent($fiatAmount, $fiatCurrency) 
                        : $fiatAmount;
                    
                    if ($rancAmount > 0) {
                        DB::transaction(function() use ($user, $rancAmount, $subscriptionCode) {
                            // Credit wallet for recurring payment
                            $this->creditSubscriptionWallet($rancAmount, $user->id, 'subscription_recurring');
                            
                            // Update subscription next renewal date
                            Subscription::where('paystack_subscription_code', $subscriptionCode)
                                ->update(['next_renewal_date' => now()->addMonth()]);
                            
                            Log::info('Wallet credited for recurring subscription', [
                                'user_id' => $user->id,
                                'subscription_code' => $subscriptionCode,
                                'ranc_amount' => $rancAmount
                            ]);
                        });
                    }
                }
            }
        }
    }
}