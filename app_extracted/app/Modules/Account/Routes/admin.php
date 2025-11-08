<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Account\Http\Controllers\AdminCreditController;

// Admin Credit Routes
Route::prefix('admin')->group(function () {
    Route::get('/credit', [AdminCreditController::class, 'index'])
        ->name('admin.credit.index');
        
    Route::post('/credit', [AdminCreditController::class, 'processCredit'])
        ->name('admin.credit.process');

    Route::post('/credit/quick/{userId}/{amount}', [AdminCreditController::class, 'quickCredit'])
        ->name('admin.credit.quick');
});
