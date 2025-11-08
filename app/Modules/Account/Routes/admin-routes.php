<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Account\Http\Controllers\AdminCreditController;

// Admin Manual Credit Routes
Route::prefix('admin')->group(function () {
    // GET: Display the admin credit interface
    Route::get('/credit', [AdminCreditController::class, 'index'])
        ->name('admin.credit.index');
        
    // POST: Process manual credit form submission
    Route::post('/credit', [AdminCreditController::class, 'processCredit'])
        ->name('admin.credit.process');

    // POST: Quick credit via AJAX (+100/+500 buttons)
    Route::post('/credit/quick', [AdminCreditController::class, 'quickCredit'])
        ->name('admin.credit.quick');

    // GET: Check user balance
    Route::get('/credit/balance/{userId}', [AdminCreditController::class, 'checkBalance'])
        ->name('admin.credit.balance');
});