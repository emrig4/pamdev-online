<?php

namespace App\Modules\Account\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ManualCreditController extends \Illuminate\Routing\Controller
{
    public function index()
    {
        try {
            // Get users with wallet balances (same as working test)
            $users = DB::table('users')
                ->leftJoin('subscription_wallets', 'users.id', '=', 'subscription_wallets.user_id')
                ->select(
                    'users.id',
                    'users.first_name', 
                    'users.last_name', 
                    'users.username', 
                    'users.email',
                    DB::raw('COALESCE(subscription_wallets.ranc, 0) as ranc')
                )
                ->get();

            return view('account::manual-credit', compact('users'));

        } catch (\Exception $e) {
            Session::flash('error', 'Error: ' . $e->getMessage());
            return view('account::manual-credit', ['users' => collect([])]);
        }
    }

    public function processCredit(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:255'
        ]);

        $email = $request->input('email');
        $amount = (float) $request->input('amount');
        $reason = $request->input('reason');

        try {
            // Find user by email (same approach as working test)
            $user = DB::table('users')->where('email', $email)->first();
            
            if (!$user) {
                Session::flash('error', 'User not found');
                return redirect()->route('manual.credit.index');
            }

            // Credit wallet using increment (same as working test)
            DB::table('subscription_wallets')
                ->updateOrInsert(
                    ['user_id' => $user->id],
                    ['ranc' => DB::raw('COALESCE(ranc, 0) + ' . $amount), 'updated_at' => now()]
                );

            // Get new balance
            $wallet = DB::table('subscription_wallets')->where('user_id', $user->id)->first();
            $newBalance = $wallet->ranc ?? 0;

            // Log transaction
            DB::table('wallet_transactions')->insert([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'credit',
                'balance_after' => $newBalance,
                'reference' => 'Manual Credit - ' . $reason,
                'description' => 'Manual wallet credit: ' . $reason,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Session::flash('success', "Successfully credited {$amount} RANC to {$user->email}. New balance: {$newBalance} RANC");

            return redirect()->route('manual.credit.index');

        } catch (\Exception $e) {
            Session::flash('error', 'Error: ' . $e->getMessage());
            return redirect()->route('manual.credit.index');
        }
    }
}
