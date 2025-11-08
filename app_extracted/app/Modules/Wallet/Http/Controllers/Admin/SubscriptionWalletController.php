<?php

namespace App\Modules\Wallet\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use App\Modules\Wallet\Http\Traits\WalletTrait;
use Yajra\DataTables\Facades\DataTables;

class SubscriptionWalletController extends Controller
{
    use WalletTrait;

    /**
     * Display manual crediting interface
     * @return Renderable
     */
    public function manualCredit()
    {
        $recentCredits = \App\Modules\Wallet\Models\CreditWalletTransaction::where('remark', 'like', 'Admin Manual Credit%')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.wallets.manual-credit', compact('recentCredits'));
    }

    /**
     * Search users for manual crediting
     * @param Request $request
     * @return JsonResponse
     */
    public function searchUsers(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json([]);
        }

        $users = User::where('email', 'like', '%' . $query . '%')
            ->orWhere('first_name', 'like', '%' . $query . '%')
            ->orWhere('last_name', 'like', '%' . $query . '%')
            ->orWhere('username', 'like', '%' . $query . '%')
            ->with('subscriptionWallet')
            ->limit(10)
            ->get();

        return response()->json($users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'current_ranc' => $user->subscriptionWallet ? $user->subscriptionWallet->ranc : 0,
                'ranc' => number_format($user->subscriptionWallet ? $user->subscriptionWallet->ranc : 0, 2),
                'formatted_name' => $user->name . ' (' . $user->email . ') - Current: ' . number_format($user->subscriptionWallet ? $user->subscriptionWallet->ranc : 0, 2) . ' RANC'
            ];
        }));
    }

    /**
     * Get user details for manual crediting
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserDetails(Request $request)
    {
        $user = User::with('subscriptionWallet')->find($request->user_id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'subscription_wallet' => $user->subscriptionWallet ? [
                'id' => $user->subscriptionWallet->id,
                'ranc' => $user->subscriptionWallet->ranc,
                'formatted_ranc' => number_format($user->subscriptionWallet->ranc, 2),
                'reference' => $user->subscriptionWallet->reference,
                'active' => $user->subscriptionWallet->active
            ] : [
                'id' => null,
                'ranc' => 0,
                'formatted_ranc' => '0.00',
                'reference' => 'N/A',
                'active' => false
            ]
        ]);
    }

    /**
     * Process manual crediting of user wallet
     * Using EXACT PROVEN TINKER METHOD
     * @param Request $request
     * @return JsonResponse
     */
    public function processCredit(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'ranc_amount' => 'required|numeric|min:0.01',
            'credit_type' => 'required|in:earning,bonus,refund,manual',
            'credit_reason' => 'nullable|string|max:255',
        ]);

        $user = User::find($request->user_id);
        $amount = (float) $request->ranc_amount;

        try {
            // EXACT PROVEN TINKER METHOD: $user->subscriptionWallet()->first()?->increment('ranc', 500);
            $subscriptionWallet = $user->subscriptionWallet;
            
            // Create wallet if doesn't exist
            if (!$subscriptionWallet) {
                $subscriptionWallet = new \App\Modules\Wallet\Models\SubscriptionWallet();
                $subscriptionWallet->user_id = $user->id;
                $subscriptionWallet->reference = \Str::uuid();
                $subscriptionWallet->ranc = 0;
                $subscriptionWallet->expiring = 0;
                $subscriptionWallet->active = true;
                $subscriptionWallet->save();
            }

            // PROVEN TINKER METHOD: Increment RANC
            $subscriptionWallet->increment('ranc', $amount);
            $subscriptionWallet->save();

            // Create transaction record
            \App\Modules\Wallet\Models\CreditWalletTransaction::create([
                'user_id' => $user->id,
                'ranc' => $amount,
                'remark' => "Admin Manual Credit - {$request->credit_type}: {$request->credit_reason}",
                'type' => 'credit',
                'previous_ranc' => $subscriptionWallet->ranc - $amount,
                'current_ranc' => $subscriptionWallet->ranc,
                'new_ranc' => $subscriptionWallet->ranc,
                'status' => 'completed'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully credited {$amount} RANC to {$user->name} ({$user->email})",
                'data' => [
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'id' => $user->id
                    ],
                    'credit_amount' => $amount,
                    'previous_balance' => $subscriptionWallet->ranc - $amount,
                    'new_balance' => $subscriptionWallet->ranc,
                    'formatted_new_balance' => number_format($subscriptionWallet->ranc, 2)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error crediting wallet: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Alias method for backward compatibility
     * @param Request $request
     * @return JsonResponse
     */
    public function processManualCredit(Request $request)
    {
        return $this->processCredit($request);
    }

    /**
     * Get credit history
     * @param Request $request
     * @return JsonResponse
     */
    public function getCreditHistory(Request $request)
    {
        $transactions = \App\Modules\Wallet\Models\CreditWalletTransaction::where('remark', 'like', 'Admin Manual Credit%')
            ->with('user')
            ->latest()
            ->limit(50);

        if ($request->ajax()) {
            $data = collect($transactions->get());
            return DataTables::of($data)
                ->addColumn('user_name', function($row) {
                    return $row->user->name ?? 'N/A';
                })
                ->addColumn('user_email', function($row) {
                    return $row->user->email ?? 'N/A';
                })
                ->addColumn('amount_formatted', function($row) {
                    return number_format($row->ranc, 2) . ' RANC';
                })
                ->addColumn('date_formatted', function($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->rawColumns([])
                ->make(true);
        }

        $recentCredits = $transactions->limit(10)->get();
        return view('admin.wallets.credit-history', compact('recentCredits'));
    }
}
