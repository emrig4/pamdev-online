<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Modules\Wallet\Http\Traits\WalletTrait;

class ProcessWalletCredits extends Command
{
    protected $signature = 'wallet:process-credits';
    protected $description = 'Process pending wallet credits from successful payments';

    public function handle()
    {
        $pendingPayments = DB::table('payments')
            ->where('txntype', 'subscription')
            ->where('status', 'success')
            ->whereNull('processed_at')
            ->get();

        $this->info("Found " . $pendingPayments->count() . " pending payments");

        foreach ($pendingPayments as $payment) {
            $rancAmount = $this->rancEquivalent($payment->amount / 100, $payment->currency);
            
            if ($rancAmount > 0) {
                try {
                    WalletTrait::creditSubscriptionWallet($rancAmount, 'subscription', $payment->user_id);
                    
                    DB::table('payments')
                        ->where('id', $payment->id)
                        ->update(['processed_at' => now()]);
                        
                    $this->info("Credited {$rancAmount} RANC to user {$payment->user_id}");
                } catch (\Exception $e) {
                    $this->error("Failed to process payment {$payment->reference}: " . $e->getMessage());
                }
            }
        }
    }

    private function rancEquivalent($fiatAmount, $fiatCurrency)
    {
        switch ($fiatCurrency) {
            case 'NGN':
                return $fiatAmount * 10;
            case 'USD':
                return $fiatAmount * 1500;
            default:
                return $fiatAmount;
        }
    }
}
