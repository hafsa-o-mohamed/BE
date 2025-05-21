<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ApartmentOwnerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ElectricityBillController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WaterBillController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes - no auth required
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return app(AuthController::class)->showLoginForm();
})->name('login');


Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // ... rest of your protected routes
});

// Logout route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Redirect authenticated users to dashboard when trying to access root


// Wrap dashboard routes in auth middleware
Route::prefix('dashboard')->middleware(['auth'])->group(function () {
    // Dashboard home - shows dashboard.index view
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Project routes - corresponds to these views:
    // GET /projects -> projects.index (list view)
    // GET /projects/create -> projects.create (creation form)
    // POST /projects -> projects.store (no view, handles creation)
    // GET /projects/{project} -> projects.show (detail view)
    // GET /projects/{project}/edit -> projects.edit (edit form)
    // PUT/PATCH /projects/{project} -> projects.update (no view, handles update)
    // DELETE /projects/{project} -> projects.destroy (no view, handles deletion)
    Route::resource('projects', ProjectController::class);

    // Building routes - same pattern as above with 'buildings' prefix
    // GET /buildings -> buildings.index
    // GET /buildings/create -> buildings.create
    // etc...
    Route::resource('buildings', BuildingController::class);
    Route::get('/buildings/{building}', [BuildingController::class, 'show'])->name('buildings.show');
    Route::get('buildings/edit', [BuildingController::class, 'edit'])->name('dashboard.buildings.edit');
    Route::delete('buildings/destroy', [BuildingController::class, 'destroy'])->name('dashboard.buildings.destroy');
    // Custom route - likely returns buildings.by_project view or JSON
    Route::get('buildings/by-project/{project}', [BuildingController::class, 'getByProject'])
        ->name('buildings.by-project');

    // Apartment routes - same pattern with 'apartments' prefix
    // GET /apartments -> apartments.index
    // GET /apartments/create -> apartments.create
    // etc...
    Route::resource('apartments', ApartmentController::class);
    // Custom route - likely returns apartments.by_building view or JSON
    Route::get('apartments/by-building/{building}', [ApartmentController::class, 'getByBuilding'])
        ->name('apartments.by-building');

    // Contract routes - same pattern with 'contracts' prefix
    // GET /contracts -> contracts.index
    // GET /contracts/create -> contracts.create
    // etc...
    Route::resource('contracts', ContractController::class);
    Route::resource('services', ServiceController::class);
    Route::resource('owners', ApartmentOwnerController::class);
    Route::resource('users', UserController::class);
    Route::put('/users/{user}/update-role', [UserController::class, 'updateRole'])->name('users.update.role');
    Route::post('/bills/create-contract-bills', [BillController::class, 'createContractBills'])->name('bills.create-contract');
    Route::post('/bills/create-from-modal', [BillController::class, 'createBillFromModal'])->name('bills.create-from-modal');
    // Route::post('/contracts/{contract}/services', [ContractServiceController::class, 'store'])
    // ->name('contract.services.store');
    // Custom route - likely returns contracts.by_project view or JSON
    Route::get('contracts/by-project/{project}', [ContractController::class, 'getByProject'])
        ->name('contracts.by-project');

    Route::get('/service-requests', [ServiceRequestController::class, 'index'])
        ->name('dashboard.service-requests.index');
    // Due Payments
    Route::get('/due-payments', [ServiceRequestController::class, 'duePayments'])->name('services.duepayments');



    Route::patch('/payments/{serviceRequest}/update-status', [PaymentController::class, 'updateStatus']);
    
    
    Route::get('/water-bills', [WaterBillController::class, 'index'])->name('water.index');
    Route::get('/electricity-bills', [ElectricityBillController::class, 'index'])->name('electricity.index');
    Route::post('/electricity-bills', [ElectricityBillController::class, 'store'])->name('electricity-bills.store');
    Route::post('/water-bills', [WaterBillController::class, 'store'])->name('water-bills.store');
    Route::get('/water-bills/last', [WaterBillController::class, 'getLastWaterBill']);
    Route::get('/electricity-bills/last', [ElectricityBillController::class, 'getLastElectricityBill']);

    Route::get('/bills/filter', [BillController::class, 'filter'])->name('bills.filter');
    Route::resource('bills', BillController::class);


    // Suggestions routes
    Route::resource('suggestions', SuggestionController::class)->except(['create', 'store', 'edit']);
    Route::post('/suggestions/{suggestionId}/replies', [SuggestionController::class, 'addReply'])->middleware('admin');
    Route::get('/suggestions/{suggestionId}/replies', [SuggestionController::class, 'getReplies'])->middleware('admin')->name('suggestions.replies');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');
    Route::post('/dashboard/suggestions/{id}/reply', [SuggestionController::class, 'storeReply'])->name('suggestions.reply');
});

Route::get('/terms', function () {
    return view('terms');
})->name('terms');

Route::get('/support', function () {
    return view('support');
});
