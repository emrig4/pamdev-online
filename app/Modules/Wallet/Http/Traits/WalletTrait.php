<?php

namespace App\Modules\Wallet\Http\Traits;

use App\Models\User;
use App\Modules\Wallet\Models\CreditWalletTransaction;
use App\Modules\Wallet\Models\SubscriptionWallet;

trait WalletTrait
{
    public static function creditSubscriptionWallet($ranc_amount, $type='earning',  $user_id = null)
    {   
        if($user_id){
            $user = User::find($user_id);
        }else{
            $user =  auth()->user();
        }
        $ranc = (int) $ranc_amount;

        // Ensure user has a subscription wallet
        if (!$user->SubscriptionWallet) {
            $user->SubscriptionWallet()->create(['ranc' => 0]);
        }
        
        $user->SubscriptionWallet->increment('ranc', $ranc);
        $user->SubscriptionWallet->save(); 
    }

    public static function debitSubscriptionWallet($ranc_amount, $user_id = null)
    {   
        $user_id ? $user = User::find($user_id) : $user = auth()->user();
        $ranc = (int) $ranc_amount;

        // Ensure user has a subscription wallet
        if (!$user->SubscriptionWallet) {
            $user->SubscriptionWallet()->create(['ranc' => 0]);
        }
        $user->SubscriptionWallet->decrement('ranc', $ranc);
        $user->SubscriptionWallet->save(); 
    }

    public static function creditWallet($ranc_amount, $user_id = null, $resource_name = null)
    {   
        $user_id ? $user = User::find($user_id) : $user =  auth()->user();
        $ranc = (int) $ranc_amount;

        // Ensure user has a credit wallet
        if (!$user->CreditWallet) {
            $user->CreditWallet()->create(['ranc' => 0]);
        }
        
        $params = [
            'amount' =>$ranc_amount,
            'ranc' => $ranc_amount,
            'currency' => 'RNC',
            'type' => 'earning',
            'status' => 'processed',
            'remark' => 'Earnings on ' . $resource_name,
            'credit_wallet_id' => $user->CreditWallet->id
        ];
        
        $user->CreditWallet->increment('ranc', $ranc);
        $user->CreditWallet->save();
        CreditWalletTransaction::create($params);
    }

    public static function refundCreditWallet($ranc_amount, $user_id = null)
    {   
        $user_id ? $user = User::find($user_id) : $user =  auth()->user();
        $ranc = (int) $ranc_amount;
        
        // Ensure user has a credit wallet
        if (!$user->CreditWallet) {
            $user->CreditWallet()->create(['ranc' => 0]);
        }
        
        $user->CreditWallet->increment('ranc', $ranc);
        $user->CreditWallet->save(); 
    }

    public static function debitWallet($ranc_amount, $user_id = null)
    {   
        $user_id ? $user = User::find($user_id) : $user =  auth()->user();
        $ranc = (int) $ranc_amount;

        // Ensure user has a credit wallet
        if (!$user->CreditWallet) {
            $user->CreditWallet()->create(['ranc' => 0]);
        }
        
        $params = [
            'ranc' => $ranc_amount,
            'type' => 'withdrawal',
            'status' => 'pending',
            'remark' => 'Withdrawal request',
            'credit_wallet_id' => $user->CreditWallet->id
        ];
        
        $transaction = CreditWalletTransaction::create($params);
        $user->CreditWallet->decrement('ranc', $ranc);
        $user->CreditWallet->save(); 

        return $transaction;
    }

    public static function subscriptionWalletBalance ($user_id = null)
    {   
        $user_id ? $user = User::find($user_id) : $user =  auth()->user();
        
        // Ensure user has a subscription wallet
        if (!$user->SubscriptionWallet) {
            $user->SubscriptionWallet()->create(['ranc' => 0]);
        }
        
        return $user->SubscriptionWallet->ranc;
    }

    public static function creditWalletBalance ($user_id = null)
    {   
        $user_id ? $user = User::find($user_id) : $user =  auth()->user();
        
        // Ensure user has a credit wallet
        if (!$user->CreditWallet) {
            $user->CreditWallet()->create(['ranc' => 0]);
        }
        
        return $user->CreditWallet->ranc;
    }
}