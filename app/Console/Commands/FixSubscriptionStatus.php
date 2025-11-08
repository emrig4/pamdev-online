<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Subscription\Models\PaystackSubscription;

class FixSubscriptionStatus extends Command
{
    protected $signature = 'subscription:fix-status';
    protected $description = 'Fix subscription statuses based on next_payment_date';

    public function handle()
    {
        $subscriptions = PaystackSubscription::all();
        $updated = 0;

        foreach ($subscriptions as $subscription) {
            $oldStatus = $subscription->status;
            $newStatus = $subscription->status; // This calls getStatusAttribute()

            if ($oldStatus !== $newStatus) {
                $subscription->update(['status' => $newStatus]);
                $updated++;
                $this->info("Updated subscription {$subscription->id}: {$oldStatus} → {$newStatus}");
            }
        }

        $this->info("Fixed {$updated} subscription statuses");
    }
}