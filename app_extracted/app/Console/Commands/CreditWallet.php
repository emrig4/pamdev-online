<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Wallet\Http\Traits\WalletTrait;

class CreditWallet extends Command
{
    use WalletTrait;

    protected $signature = 'wallet:credit {email} {amount} {reason}';
    protected $description = 'Credit RANC wallet for a user by email';

    public function handle()
    {
        $userEmail = $this->argument('email');
        $amount = $this->argument('amount');
        $reason = $this->argument('reason');

        $user = \App\Models\User::where('email', $userEmail)->first();

        if (!$user) {
            $this->error("❌ User with email '{$userEmail}' not found!");
            $this->info("Available users:");
            
            $users = \App\Models\User::select('id', 'name', 'email')->limit(5)->get();
            foreach ($users as $userItem) {
                $this->info("  {$userItem->name} ({$userItem->email})");
            }
            
            return 1;
        }

        try {
            $this->creditSubscriptionWallet($amount);
            
            $this->info("✅ Successfully credited {$amount} RANC to {$user->name}");
            $this->info("📧 Email: {$user->email}");
            $this->info("💬 Reason: {$reason}");
            
            if ($user->subscriptionWallet) {
                $this->info("💰 New balance: {$user->subscriptionWallet->ranc} RANC");
            }
            
            $this->info("🎉 Transaction completed successfully!");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to credit wallet: " . $e->getMessage());
            return 1;
        }
    }
}
