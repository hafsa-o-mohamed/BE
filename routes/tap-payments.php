<?php

use App\Http\Controllers\Api\TapPaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tap Payments API Routes
|--------------------------------------------------------------------------
|
| This file contains all routes related to Tap Payments integration.
| These routes are loaded by the RouteServiceProvider and assigned to the "api" middleware group.
|
*/

// Public routes - webhooks don't need authentication
Route::post('/webhook', [TapPaymentController::class, 'webhook']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Create a new charge
    Route::post('/charges', [TapPaymentController::class, 'createCharge']);
    
    // Get charge details
    Route::get('/charges/{chargeId}', [TapPaymentController::class, 'getCharge']);
});
