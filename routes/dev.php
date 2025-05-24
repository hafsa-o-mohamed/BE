<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DevController;

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

// Protected dev routes
Route::middleware('dev.auth')->group(function () {
    Route::get('/dev', [DevController::class, 'index'])->name('dev.index');
    Route::post('/dev/logout', [DevController::class, 'logout'])->name('dev.logout');
}); 