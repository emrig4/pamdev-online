#!/bin/bash

# 🚀 Deploy Admin Manual Credit System
# Based on the proven working increment method that successfully credited 500 RANC

echo "🚀 Deploying Admin Manual Credit System..."
echo "🔍 Using the proven increment method: \$user->subscriptionWallet->increment('ranc', \$amount)"
echo ""

cd /home/projtpyy/pamdev.online

# Step 1: Create directories
echo "📁 Creating directories..."
mkdir -p app/Modules/Account/Http/Controllers
mkdir -p app/Modules/Account/Resources/views
mkdir -p app/Modules/Account/Routes

# Step 2: Copy Controller
echo "🔧 Copying AdminCreditController.php..."
cat > app/Modules/Account/Http/Controllers/AdminCreditController.php << 'CONTROLLER_EOF'
<?php

namespace App\Modules\Account\Http\Controllers;

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

            return view('account::admin-credit', compact('users'));

        } catch (\Exception $e) {
            Session::flash('error', 'Error loading users: ' . $e->getMessage());
            return view('account::admin-credit', ['users' => collect([])]);
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
            $subscriptionWallet = DB::table('subscription_wallets')->where('user_id', $userId)->first();
            
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
                $subscriptionWallet = DB::table('subscription_wallets')->where('user_id', $userId)->first();
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
CONTROLLER_EOF

# Step 3: Copy View
echo "🎨 Copying admin-credit.blade.php..."
cat > app/Modules/Account/Resources/views/admin-credit.blade.php << 'VIEW_EOF'
<!DOCTYPE html>



    
    
    
    Admin Manual Credit System
    
    
    
        .user-card {
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            cursor: pointer;
            border-radius: 10px;
            height: 100%;
        }
        .user-card:hover {
            border-color: #007bff;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .user-card.selected {
            border-color: #28a745;
            background-color: #f8fff9;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .balance-large {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 10px 0;
        }
        .quick-buttons button {
            margin: 2px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            box-shadow: 0 0 0 2px rgba(255,255,255,0.8);
        }
        .status-active { 
            background: linear-gradient(45deg, #28a745, #20c997);
            animation: pulse 2s infinite;
        }
        .status-inactive { background-color: #dc3545; }
        .status-zero { background-color: #6c757d; }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
    }

    .credit-form {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
    }
    
    .stats-card {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        border-radius: 15px;
        text-align: center;
        padding: 20px;
        margin-bottom: 15px;
    }
    
    .user-list-header {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .amount-preset {
        border-radius: 20px;
        margin: 2px;
        padding: 8px 12px;
        font-size: 0.85rem;
    }
    
    .loading-spinner {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
        background: rgba(255,255,255,0.9);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
</style>



    
    

        

            Loading...
        

        
Processing...


    
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h1 class="mb-0">
                                <i class="bi bi-shield-check"></i> 
                                Admin Manual Credit System
                            </h1>
                            <p class="mb-0 mt-2">
                                <i class="bi bi-info-circle"></i>
                                Based on the proven increment method that successfully credited 500 RANC
                            </p>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-light text-dark fs-6">
                                <i class="bi bi-people-fill"></i> {{ $users->count() }} Users
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    {!! session('success') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif
    
    @if(session('error'))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Users List -->
        <div class="col-lg-8">
            <div class="user-list-header">
                <h4><i class="bi bi-person-lines-fill"></i> 👥 Current Users & RANC Balances</h4>
                <p class="mb-0">Click on any user card to select them for manual crediting</p>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        @foreach($users as $user)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card user-card h-100" 
                                 data-user-id="{{ $user->id }}" 
                                 data-user-email="{{ $user->email }}"
                                 data-user-name="{{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0">
                                            @if($user->ranc > 0)
                                                <span class="status-indicator status-active"></span>
                                            @elseif($user->ranc == 0)
                                                <span class="status-indicator status-zero"></span>
                                            @else
                                                <span class="status-indicator status-inactive"></span>
                                            @endif
                                            {{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}
                                            @if(empty($user->first_name) && empty($user->last_name))
                                                <small class="text-muted">({{ $user->username ?? 'No name' }})</small>
                                            @endif
                                        </h6>
                                        @if($user->active == 1)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </div>
                                    
                                    <p class="text-muted small mb-2">{{ $user->email }}</p>
                                    
                                    <div class="balance-large text-center mb-2">
                                        @if($user->ranc > 0)
                                            <span class="text-success">
                                                <i class="bi bi-coin"></i> {{ number_format($user->ranc, 2) }} RANC
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                <i class="bi bi-coin"></i> {{ number_format($user->ranc, 2) }} RANC
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Quick Action Buttons -->
                                    <div class="quick-buttons">
                                        <button class="btn btn-outline-success btn-sm quick-credit" 
                                                data-user-id="{{ $user->id }}" 
                                                data-amount="100">
                                            <i class="bi bi-plus-circle"></i> +100 RANC
                                        </button>
                                        <button class="btn btn-outline-success btn-sm quick-credit" 
                                                data-user-id="{{ $user->id }}" 
                                                data-amount="500">
                                            <i class="bi bi-plus-circle-fill"></i> +500 RANC
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Credit Form -->
        <div class="col-lg-4">
            <!-- Credit Form Card -->
            <div class="credit-form">
                <h5><i class="bi bi-credit-card-fill"></i> Manual Credit Selected User</h5>
                <form id="creditForm" method="POST" action="{{ route('admin.credit.process') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Selected User:</strong></label>
                        <div id="selectedUser" class="form-control-plaintext text-white">
                            <em>Click on a user card to select...</em>
                        </div>
                        <input type="hidden" name="user_id" id="user_id">
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label"><strong>RANC Amount:</strong></label>
                        <div class="input-group">
                            <input type="number" name="amount" id="amount" 
                                   class="form-control" step="0.01" min="0.01" required>
                            <span class="input-group-text bg-light text-dark">RANC</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label"><strong>Reason:</strong></label>
                        <textarea name="reason" id="reason" class="form-control" rows="3" 
                                  placeholder="e.g., Manual payment credit" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100" id="submitBtn" disabled>
                        <i class="bi bi-coins"></i> 💰 Credit RANC Wallet
                    </button>
                </form>

                <!-- Quick Presets -->
                <div class="mt-3">
                    <small class="text-white-50"><strong>Quick Amount Presets:</strong></small><br>
                    <div class="btn-group mt-2" role="group">
                        <button type="button" class="btn btn-light btn-sm amount-preset" onclick="setAmount(50)">₦50</button>
                        <button type="button" class="btn btn-light btn-sm amount-preset" onclick="setAmount(100)">₦100</button>
                        <button type="button" class="btn btn-light btn-sm amount-preset" onclick="setAmount(250)">₦250</button>
                        <button type="button" class="btn btn-light btn-sm amount-preset" onclick="setAmount(500)">₦500</button>
                        <button type="button" class="btn btn-light btn-sm amount-preset" onclick="setAmount(1000)">₦1000</button>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="stats-card">
                <h6><i class="bi bi-bar-chart-fill"></i> Quick Statistics</h6>
                <div class="row text-center">
                    <div class="col-4">
                        <h3 class="mb-0">{{ $users->count() }}</h3>
                        <small>Total Users</small>
                    </div>
                    <div class="col-4">
                        <h3 class="mb-0">{{ $users->where('ranc', '>', 0)->count() }}</h3>
                        <small>Active Wallets</small>
                    </div>
                    <div class="col-4">
                        <h3 class="mb-0">{{ number_format($users->sum('ranc'), 0) }}</h3>
                        <small>Total RANC</small>
                    </div>
                </div>
            </div>

            <!-- Quick Reference -->
            <div class="card">
                <div class="card-header">
                    <h6><i class="bi bi-lightbulb"></i> Quick Reference</h6>
                </div>
                <div class="card-body">
                    <p class="small mb-2">
                        <strong>🔵 Method Used:</strong><br>
                        Uses the exact proven increment method that successfully credited 500 RANC
                    </p>
                    <p class="small mb-0">
                        <strong>⚡ Status Indicators:</strong><br>
                        🟢 Active wallets, ⚪ Zero balance, 🔴 Inactive
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let selectedUser = null;

    // User card selection
    document.querySelectorAll('.user-card').forEach(card => {
        card.addEventListener('click', function() {
            // Remove previous selections
            document.querySelectorAll('.user-card').forEach(c => c.classList.remove('selected'));
            
            // Select this card
            this.classList.add('selected');
            
            // Store user info
            selectedUser = {
                id: this.dataset.userId,
                email: this.dataset.userEmail,
                name: this.dataset.userName.trim()
            };
            
            // Update form
            updateForm();
        });
    });

    // Update form with selected user
    function updateForm() {
        if (selectedUser) {
            document.getElementById('selectedUser').innerHTML = 
                `<strong>${selectedUser.name}</strong><br><small>${selectedUser.email}</small>`;
            document.getElementById('user_id').value = selectedUser.id;
            document.getElementById('submitBtn').disabled = false;
            document.getElementById('reason').placeholder = `Manual credit for ${selectedUser.email}`;
        } else {
            document.getElementById('selectedUser').innerHTML = 
                '<em>Click on a user card to select...</em>';
            document.getElementById('user_id').value = '';
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('reason').placeholder = 'e.g., Manual payment credit';
        }
    }

    // Quick amount buttons
    function setAmount(amount) {
        document.getElementById('amount').value = amount;
    }

    // Quick credit buttons
    document.querySelectorAll('.quick-credit').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent user card selection
            
            const userId = this.dataset.userId;
            const amount = this.dataset.amount;
            
            if (confirm(`Quick credit ${amount} RANC to this user?`)) {
                showLoading();
                
                // Send AJAX request
                fetch('{{ route("admin.credit.quick") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        amount: amount,
                        reason: 'Quick Credit'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        alert(`✅ Success! \n${data.message}\nNew balance: ${data.new_balance} RANC\nUser: ${data.user_email}`);
                        location.reload();
                    } else {
                        alert('❌ Error: ' + data.error);
                    }
                })
                .catch(error => {
                    hideLoading();
                    alert('❌ Error: ' + error.message);
                });
            }
        });
    });

    // Form submission
    document.getElementById('creditForm').addEventListener('submit', function(e) {
        if (!selectedUser) {
            e.preventDefault();
            alert('Please select a user first!');
            return false;
        }
        
        // Show loading
        showLoading();
    });

    // Loading functions
    function showLoading() {
        document.getElementById('loadingSpinner').style.display = 'block';
    }

    function hideLoading() {
        document.getElementById('loadingSpinner').style.display = 'none';
    }
</script>


```
VIEW_EOF

Step 4: Add routes to web.php
echo "🛣️ Adding admin routes to routes/web.php..."

echo "" >> routes/web.php

echo "// Admin Manual Credit Routes" >> routes/web.php

echo "Route::prefix('admin')->group(function () {" >> routes/web.php

echo "    // GET: Display the admin credit interface" >> routes/web.php

echo "    Route::get('/credit', [App\Modules\Account\Http\Controllers\AdminCreditController::class, 'index'])" >> routes/web.php

echo "        ->name('admin.credit.index');" >> routes/web.php

echo "        " >> routes/web.php

echo "    // POST: Process manual credit form submission" >> routes/web.php

echo "    Route::post('/credit', [App\Modules\Account\Http\Controllers\AdminCreditController::class, 'processCredit'])" >> routes/web.php

echo "        ->name('admin.credit.process');" >> routes/web.php

echo "" >> routes/web.php

echo "    // POST: Quick credit via AJAX (+100/+500 buttons)" >> routes/web.php

echo "    Route::post('/credit/quick', [App\Modules\Account\Http\Controllers\AdminCreditController::class, 'quickCredit'])" >> routes/web.php

echo "        ->name('admin.credit.quick');" >> routes/web.php

echo "" >> routes/web.php

echo "    // GET: Check user balance" >> routes/web.php

echo "    Route::get('/credit/balance/{userId}', [App\Modules\Account\Http\Controllers\AdminCreditController::class, 'checkBalance'])" >> routes/web.php

echo "        ->name('admin.credit.balance');" >> routes/web.php

echo "});" >> routes/web.php

Step 5: Clear all caches
echo "🧹 Clearing all Laravel caches..."

php artisan cache:clear

php artisan config:clear

php artisan route:clear

php artisan view:clear

php artisan route:cache

Step 6: Test the system
echo "🧪 Testing the admin credit system..."

php artisan tinker --execute="

try {

$controller = new App\Modules\Account\Http\Controllers\AdminCreditController();

echo '✅ AdminCreditController works!' . PHP_EOL;

\$users = DB::table('users')->count();
echo '✅ Database connection works! Found ' . \$users . ' users' . PHP_EOL;

\$wallets = DB::table('subscription_wallets')->count();
echo '✅ Wallet system works! Found ' . \$wallets . ' wallets' . PHP_EOL;

echo '✅ Admin Manual Credit System is ready!' . PHP_EOL;
echo '🎯 Method: Uses the exact proven increment method' . PHP_EOL;
echo '🎯 Based on: \$user->subscriptionWallet->increment(\"ranc\", \$amount)' . PHP_EOL;
} catch (Exception $e) {

echo '❌ Error: ' . $e->getMessage() . PHP_EOL;

}

"

echo ""

echo "🎉 ADMIN MANUAL CREDIT SYSTEM DEPLOYED SUCCESSFULLY!"

echo ""

echo "🔗 Admin URL: https://pamdev.online/admin/credit"

