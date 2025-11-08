<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AdminCreditController extends \Illuminate\Routing\Controller
{
    /**
     * Display the admin manual credit interface
     * Shows all users with their current RANC balances
     */
    public function index()
    {
        try {
            // Get all users with their wallet balances (using the same proven method)
            $users = DB::table('users')
                ->leftJoin('subscription_wallets', 'users.id', '=', 'subscription_wallets.user_id')
                ->select(
                    'users.id',
                    'users.first_name', 
                    'users.last_name', 
                    'users.username', 
                    'users.email',
                    DB::raw('COALESCE(subscription_wallets.ranc, 0) as ranc'),
                    DB::raw('COALESCE(subscription_wallets.active, 0) as active')
                )
                ->orderBy('users.last_name')
                ->orderBy('users.first_name')
                ->get();

            return view('admin-credit', compact('users'));

        } catch (\Exception $e) {
            Session::flash('error', 'Error loading users: ' . $e->getMessage());
            return view('admin-credit', ['users' => collect([])]);
        }
    }

    /**
     * Process manual credit request
     * Uses the EXACT proven increment method
     */
    public function processCredit(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:255'
        ]);

        $userId = (int) $request->input('user_id');
        $amount = (float) $request->input('amount');
        $reason = $request->input('reason');

        try {
            // Find user by ID (same as working test)
            $user = DB::table('users')->where('id', $userId)->first();
            
            if (!$user) {
                Session::flash('error', 'User not found');
                return redirect()->route('admin.credit.index');
            }

            // Get user's current wallet or create one if missing
            $subscriptionWallet = DB::table('subscription_wallets')->where('user_id', 
$userId)->first();
            
            if (!$subscriptionWallet) {
                // Auto-create wallet if doesn't exist (like in the working test)
                DB::table('subscription_wallets')->insert([
                    'user_id' => $userId,
                    'reference' => 'ADMIN_MANUAL_' . uniqid(),
                    'ranc' => 0.00,
                    'active' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $subscriptionWallet = DB::table('subscription_wallets')->where('user_id', 
$userId)->first();
            }

            $balanceBefore = $subscriptionWallet->ranc ?? 0;

            // USE THE EXACT PROVEN INCREMENT METHOD (like the 500 RANC test)
            DB::table('subscription_wallets')
                ->where('user_id', $userId)
                ->increment('ranc', $amount);

            // Get the new balance after increment
            $wallet = DB::table('subscription_wallets')->where('user_id', $userId)->first();
            $newBalance = $wallet->ranc ?? 0;

            // Log the transaction for tracking
            DB::table('wallet_transactions')->insert([
                'user_id' => $userId,
                'amount' => $amount,
                'type' => 'credit',
                'balance_after' => $newBalance,
                'reference' => 'Manual Admin Credit - ' . $reason,
                'description' => 'Manual wallet credit by admin: ' . $reason,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Format user name
            $userName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            if (empty($userName)) $userName = $user->username ?? $user->email;

            // Success message
            Session::flash('success', 
                "✅ Successfully credited {$amount} RANC to {$userName} ({$user->email})!<br>" .
                "💰 Balance changed: {$balanceBefore} → {$newBalance} RANC<br>" .
                "📝 Reason: {$reason}<br>" .
                "🔗 Transaction ID: " . (DB::getPdo()->lastInsertId())
            );

            return redirect()->route('admin.credit.index');

        } catch (\Exception $e) {
            Session::flash('error', '❌ Error: ' . $e->getMessage());
            return redirect()->route('admin.credit.index');
        }
    }

    /**
     * Quick credit via AJAX (for +100/+500 buttons)
     * Uses the proven increment method
     */
    public function quickCredit(Request $request)
    {
        $userId = $request->input('user_id');
        $amount = (float) $request->input('amount');
        $reason = $request->input('reason', 'Quick Credit');

        try {
            $user = DB::table('users')->where('id', $userId)->first();
            
            if (!$user) {
                return response()->json(['success' => false, 'error' => 'User not found']);
            }

            // USE THE PROVEN INCREMENT METHOD
            DB::table('subscription_wallets')
                ->where('user_id', $userId)
                ->increment('ranc', $amount);

            $wallet = DB::table('subscription_wallets')->where('user_id', $userId)->first();
            $newBalance = $wallet->ranc ?? 0;

            // Log transaction
            DB::table('wallet_transactions')->insert([
                'user_id' => $userId,
                'amount' => $amount,
                'type' => 'credit',
                'balance_after' => $newBalance,
                'reference' => 'Quick Credit - ' . $reason,
                'description' => 'Quick manual credit: ' . $reason,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Credited {$amount} RANC successfully",
                'new_balance' => $newBalance,
                'user_email' => $user->email
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Check user balance
     */
    public function checkBalance($userId)
    {
        try {
            $wallet = DB::table('subscription_wallets')
                ->join('users', 'subscription_wallets.user_id', '=', 'users.id')
                ->where('subscription_wallets.user_id', $userId)
                ->select(
                    'users.email',
                    'users.first_name',
                    'users.last_name',
                    'subscription_wallets.ranc',
                    'subscription_wallets.active'
                )
                ->first();

            if ($wallet) {
                return response()->json([
                    'success' => true,
                    'data' => $wallet
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Wallet not found'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
