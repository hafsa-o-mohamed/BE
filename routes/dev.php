<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DevController;
use App\Http\Controllers\DevPaymentController;

/*
|--------------------------------------------------------------------------
| Dev Routes
|--------------------------------------------------------------------------
|
| Here are routes for development purposes. These should only be used
| in development environments.
|
*/

// Dev login route (no auth required)
Route::get('/dev/login', [DevController::class, 'login'])->name('dev.login');
Route::post('/dev/authenticate', [DevController::class, 'authenticate'])->name('dev.authenticate');

// Webhook and Redirect routes (no auth required - external calls)
Route::prefix('dev/payments')->name('dev.payments.')->group(function () {
    Route::post('/webhook', [DevPaymentController::class, 'webhook'])->name('webhook');
    Route::get('/redirect', [DevPaymentController::class, 'redirect'])->name('redirect');
});

// Protected dev routes
Route::middleware('dev.auth')->group(function () {
    Route::get('/dev', [DevController::class, 'index'])->name('dev.index');
    Route::post('/dev/logout', [DevController::class, 'logout'])->name('dev.logout');
    
    // Payment testing routes
    Route::prefix('dev/payments')->name('dev.payments.')->group(function () {
        Route::get('/test-cards', [DevPaymentController::class, 'testCards'])->name('test-cards');
        Route::get('/tokens', [DevPaymentController::class, 'tokens'])->name('tokens');
        Route::post('/tokens/create', [DevPaymentController::class, 'createToken'])->name('tokens.create');
        Route::get('/charges', [DevPaymentController::class, 'charges'])->name('charges');
        Route::post('/charges/create', [DevPaymentController::class, 'createCharge'])->name('charges.create');
        Route::get('/webhook-logs', [DevPaymentController::class, 'webhookLogs'])->name('webhook-logs');
    });
}); 
Route::post('/dev/payments/tokens/apple-pay', [DevPaymentController::class, 'createApplePayToken'])->name('tokens.apple-pay');