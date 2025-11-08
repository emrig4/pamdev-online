<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('payment')->group(function() {
    Route::get('/', 'PaymentController@index');

    // Paystack
    Route::post('/paystack/pay', 'PaystackPaymentController@redirectToGateway')->name('paystack.pay');

    // Fixed callback route name to avoid conflict
    Route::get('/paystack/callback', [PaystackPackagesController::class, 'paymentCallback'])
         ->name('paystack.packages.callback');
});
