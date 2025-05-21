<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\BuildingController;
use App\Http\Controllers\Api\ApartmentController;
use App\Http\Controllers\Api\MaintenanceServiceController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\ServiceRequestController;
use App\Http\Controllers\Api\SuggestionController;
use App\Http\Controllers\Api\ServicesController;
use App\Http\Controllers\Api\ContractServicesController;
use App\Http\Controllers\Api\AllBillsController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\AdminNotificationController;
use App\Http\Controllers\Api\UnitRegistrationController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
 // Route for updating password
 Route::put('/update-password', [AuthController::class, 'updatePassword'])->middleware('auth');

 // Route for updating email
 Route::post('/update-email', [AuthController::class, 'updateEmail'])->middleware('auth');
 // Route for updating phone number
    Route::post('/update-phone', [AuthController::class, 'updatePhone'])->middleware('auth');
    // User Profile
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::post('/profile/update-phone', [UserController::class, 'updatePhone']);

    // Projects
    Route::apiResource('projects', ProjectController::class)->only(['index', 'show']);

    // Services
    Route::get('/services', [ServicesController::class, 'index']);
    Route::get('/services/{id}', [ServicesController::class, 'show']);

    // Maintenance Services
    Route::get('/maintenance-services', [MaintenanceServiceController::class, 'index']);
    Route::get('/maintenance-services/{id}', [MaintenanceServiceController::class, 'show']);
    // Buildings
    Route::apiResource('buildings', BuildingController::class)->only(['index', 'show']);

    // Apartments
    Route::apiResource('apartments', ApartmentController::class)->only(['index', 'show']);

    // Maintenance Services
    Route::get('/maintenance-services', [MaintenanceServiceController::class, 'index']);

    // Contracts
    Route::get('/contracts', [ContractController::class, 'index']);
    Route::post('/contracts', [ContractController::class, 'store']);
    Route::get('/contracts/{id}', [ContractController::class, 'show']);
    // Service Requests
    Route::get('/service-requests', [ServiceRequestController::class, 'index']);
    Route::post('/service-requests', [ServiceRequestController::class, 'store']);
    // Route::put('/service-requests/{request_id}', [ServiceRequestController::class, 'update']);
    
    // Payment
    Route::post('/create-payment', [PaymentController::class, 'create']);
    // User settings
    Route::put('/user/password', [UserController::class, 'updatePassword']);
    
    // Suggestions
    Route::post('/suggestions', [SuggestionController::class, 'store']);
    Route::get('/suggestions', [SuggestionController::class, 'index']);
    Route::get('/suggestions/{id}', [SuggestionController::class, 'show']);
    Route::patch('/suggestions/{id}/status', [SuggestionController::class, 'updateStatus']);
    Route::get('/suggestions/user/{userId}', [SuggestionController::class, 'getByUser']);
    Route::get('/suggestions/{suggestionId}/replies', [SuggestionController::class, 'getReplies']);

    // Active Contract
    Route::get('/active-contract', [ContractServicesController::class, 'getActiveContract']);

    // Unpaid Services
    Route::get('/service-requests/unpaid', [AllBillsController::class, 'getServiceRequests']);

    Route::get('/unpaid-count', [AllBillsController::class, 'getUnpaidCount']); // Add this line

    // Bills
    Route::get('/bills', [AllBillsController::class, 'getBills']);
    Route::get('/bills/total', [AllBillsController::class, 'getTotalAmount']); // Add this line for combined total
   
    // Unit Registration
    Route::post('/register-unit', [UnitRegistrationController::class, 'register']);
// Admin routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/service-requests', [AdminController::class, 'getServiceRequests']);
    Route::patch('/service-requests/{id}/status', [AdminController::class, 'updateServiceStatus']);
    Route::get('/service-requests/stats', [AdminController::class, 'getServiceStats']);
    Route::get('/projects/buildings', [AdminController::class, 'getProjectsWithBuildings']);
    Route::get('/latest-service-requests', [AdminController::class, 'getLatestServiceRequests']);
    Route::get('/buildings-negative-bills', [AdminController::class, 'getBuildingsWithNegativeBills']);
    Route::get('/latest-projects', [AdminController::class, 'getLatestProjects']);
    Route::get('/unresolved-items', [AdminController::class, 'getUnresolvedItems']);
    Route::get('/buildings/{id}/water-bills', [AdminController::class, 'getBuildingWaterBills']);
    Route::get('/buildings/{id}', [AdminController::class, 'getBuilding']);
    Route::get('/buildings/{id}/electricity-bills', [AdminController::class, 'getBuildingElectricityBills']);
    Route::get('/buildings/{id}/contract', [AdminController::class, 'getBuildingContract']);
    Route::post('/notifications/send-all', [AdminNotificationController::class, 'sendToAll']);
    Route::post('/notifications/send-to-user', [AdminNotificationController::class, 'sendToUser']);
});

    Route::prefix('notifications')->group(function () {
        Route::post('/update-token', [NotificationController::class, 'updateDeviceToken']);
        Route::post('/send-all', [NotificationController::class, 'sendToAll'])->middleware('admin');
    });

    Route::post('/notifications/update-token', [NotificationController::class, 'updateToken']);
    Route::post('/notifications/send', [NotificationController::class, 'sendNotification']);

});



