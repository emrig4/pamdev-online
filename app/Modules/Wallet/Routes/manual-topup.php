<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Wallet\Http\Controllers\Admin\ManualTopUpController;
use App\Modules\Wallet\Http\Controllers\Admin\CreditWalletController;

/*
|--------------------------------------------------------------------------
| Admin Wallet Routes
|--------------------------------------------------------------------------
*/

// Manual Top-Up Routes
Route::middleware(['web', 'auth', 'role:sudo|admin'])->group(function () {
    Route::group(['prefix' => 'admin/manual-topup', 'as' => 'admin.manual-topup.'], function () {
        // Main dashboard
        Route::get('/', [ManualTopUpController::class, 'index'])->name('index');
        
        // Create new top-up
        Route::get('/create', [ManualTopUpController::class, 'create'])->name('create');
        Route::post('/store', [ManualTopUpController::class, 'store'])->name('store');
        
        // History and listing
        Route::get('/history', [ManualTopUpController::class, 'history'])->name('history');
        
        // Transaction details
        Route::get('/{id}/show', [ManualTopUpController::class, 'show'])->name('show');
        
        // Actions
        Route::post('/{id}/approve', [ManualTopUpController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [ManualTopUpController::class, 'reject'])->name('reject');
        
        // Utility endpoints
        Route::get('/users/search', [ManualTopUpController::class, 'searchUsers'])->name('users.search');
        Route::get('/users/{userId}/balances', [ManualTopUpController::class, 'getUserBalances'])->name('users.balances');
    });
});

// Existing Credit Wallet Routes (keeping all existing routes)
Route::middleware(['web', 'auth', 'role:sudo|admin'])->group(function () {
    Route::group(['prefix' => 'admin/wallets', 'as' => 'admin.'], function () {
        // Existing wallet routes
        Route::get('/transactions', [CreditWalletController::class, 'walletTransaction'])->name('wallets.transactions');
        Route::post('/withdraw', [CreditWalletController::class, 'withdraw'])->name('wallets.withdraw');
        Route::post('/credit', [CreditWalletController::class, 'credit'])->name('wallets.credit');
        Route::get('/withdrawals', [CreditWalletController::class, 'walletTransaction'])->name('wallets.withdrawals');
        Route::get('/earnings', [CreditWalletController::class, 'walletTransaction'])->name('wallets.earnings');
        Route::get('/{wallet}', [CreditWalletController::class, 'show'])->name('wallets.show');
        
        // Withdrawal processing routes
        Route::get('/transactions/{transaction}/reject', [CreditWalletController::class, 'rejectWithdrawal'])->name('reject-withdrawal');
        Route::get('/transactions/{transaction}/process', [CreditWalletController::class, 'processWithdrawal'])->name('process-withdrawal');
    });
});

// API Routes for AJAX operations
Route::middleware(['web', 'auth', 'role:sudo|admin'])->group(function () {
    Route::group(['prefix' => 'api/wallet', 'as' => 'admin.api.wallet.'], function () {
        Route::post('/topup/bulk', [ManualTopUpController::class, 'bulkTopUp'])->name('topup.bulk');
        Route::get('/stats', [ManualTopUpController::class, 'getStats'])->name('stats');
        Route::post('/export/history', [ManualTopUpController::class, 'exportHistory'])->name('export.history');
    });
});