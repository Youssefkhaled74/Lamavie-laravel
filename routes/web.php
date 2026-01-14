<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Dashboard\Admin\AdminController;
use App\Http\Controllers\Dashboard\Admin\LoginController;
use App\Http\Controllers\Dashboard\Admin\LogoutController;
use App\Http\Controllers\Dashboard\Admin\BookingController;
use App\Http\Controllers\Dashboard\Admin\BookingCarAssignmentController;
use App\Http\Controllers\Dashboard\Admin\CarTimelineController;
use App\Http\Controllers\Dashboard\Admin\ServiceController;
use App\Http\Controllers\Dashboard\Admin\SettingsController;
use App\Http\Controllers\Dashboard\Admin\DashboardController;
use App\Http\Controllers\Dashboard\Admin\FrequencyController;
use App\Http\Controllers\Dashboard\Admin\YourItemsController;
use App\Http\Controllers\Dashboard\Admin\CarpetSizeController;
use App\Http\Controllers\Dashboard\Admin\FabricTypeController;
use App\Http\Controllers\Dashboard\Admin\MeasurementController;
use App\Http\Controllers\Dashboard\Admin\ServiceTypeController;
use App\Http\Controllers\Dashboard\Admin\SizeOfStainController;
use App\Http\Controllers\Dashboard\Admin\TypeOfStainController;
use App\Http\Controllers\Dashboard\Admin\PaymentMethodController;
use App\Http\Controllers\Dashboard\Admin\TypeOfPackageController;
use App\Http\Controllers\Dashboard\Admin\CarpetMaterialController;
use App\Http\Controllers\Dashboard\Admin\EstimatedHoursController;
use App\Http\Controllers\Dashboard\Admin\ServiceCategoryController;
use App\Http\Controllers\Dashboard\Admin\NumberOfCleanersController;
use App\Http\Controllers\Dashboard\Admin\PackagesOptionalController;
use App\Http\Controllers\Dashboard\Admin\LevelOfInfestationController;
use App\Http\Controllers\Dashboard\Admin\PlaceOfTheCleaningController;
use App\Http\Controllers\Dashboard\Admin\TypeOfServiceNeededController;
use App\Http\Controllers\Dashboard\Admin\CarsAdditionalServiceController;
use App\Http\Controllers\Dashboard\Admin\MaintenanceOrCleaningController;
use App\Http\Controllers\Dashboard\Admin\PresenceOfChildrenOrPetsController;
use App\Http\Controllers\Dashboard\Admin\HomeBannerController as AdminHomeBannerController;
use App\Http\Controllers\Dashboard\Admin\AreaController;
use App\Http\Controllers\Dashboard\Admin\FcmTokenController;
use App\Http\Controllers\Dashboard\Admin\NotificationApiController;
use App\Http\Controllers\Dashboard\Admin\LogsController;
use Illuminate\Http\Request;


Route::get('/', function () {
    if (Auth::guard('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('admin.login');
});

// Driver-facing routes
Route::prefix('driver')->name('driver.')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Driver\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Driver\AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [\App\Http\Controllers\Driver\AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [\App\Http\Controllers\Driver\DashboardController::class, 'index'])->name('dashboard')->middleware('auth:driver');
    // Driver bookings
    Route::get('/bookings', [\App\Http\Controllers\Driver\BookingController::class, 'index'])->name('bookings.index')->middleware('auth:driver');
    Route::get('/bookings/{booking}', [\App\Http\Controllers\Driver\BookingController::class, 'show'])->name('bookings.show')->middleware('auth:driver');
    Route::put('/bookings/{booking}', [\App\Http\Controllers\Driver\BookingController::class, 'update'])->name('bookings.update')->middleware('auth:driver');
    // Lab-related driver actions
    Route::post('/bookings/{booking}/assign-lab', [\App\Http\Controllers\Driver\BookingController::class, 'assignLab'])->name('bookings.assignLab')->middleware('auth:driver');
    Route::post('/bookings/{booking}/arrived-at-lab', [\App\Http\Controllers\Driver\BookingController::class, 'markArrivedAtLab'])->name('bookings.arrivedAtLab')->middleware('auth:driver');
    Route::post('/bookings/{booking}/picked-from-lab', [\App\Http\Controllers\Driver\BookingController::class, 'markPickedFromLab'])->name('bookings.pickedFromLab')->middleware('auth:driver');
    Route::post('/bookings/{booking}/driver-collected', [\App\Http\Controllers\Driver\BookingController::class, 'markDriverCollected'])->name('bookings.driverCollected')->middleware('auth:driver');
    Route::post('/bookings/{booking}/returned-to-user', [\App\Http\Controllers\Driver\BookingController::class, 'markReturnedToUser'])->name('bookings.returnedToUser')->middleware('auth:driver');
    
    // Driver-facing lab profile
    Route::get('/labs/{lab}', [\App\Http\Controllers\Driver\LabController::class, 'show'])->name('labs.show')->middleware('auth:driver');
});

// Lab-facing routes
Route::prefix('lab')->name('lab.')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Lab\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Lab\AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [\App\Http\Controllers\Lab\AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [\App\Http\Controllers\Lab\DashboardController::class, 'index'])->name('dashboard')->middleware('auth:lab');
    Route::get('/bookings', [\App\Http\Controllers\Lab\DashboardController::class, 'index'])->name('bookings.index')->middleware('auth:lab');
    Route::get('/bookings/{booking}', [\App\Http\Controllers\Lab\DashboardController::class, 'showBooking'])->name('bookings.show')->middleware('auth:lab');
    // Lab lifecycle actions
    Route::post('/bookings/{booking}/arrived-at-lab', [\App\Http\Controllers\Lab\BookingController::class, 'markArrivedAtLab'])->name('bookings.arrivedAtLab')->middleware('auth:lab');
    Route::post('/bookings/{booking}/picked-from-lab', [\App\Http\Controllers\Lab\BookingController::class, 'markPickedFromLab'])->name('bookings.pickedFromLab')->middleware('auth:lab');
});

// Emergency cache clear route - remove after use
Route::get('/clear-all-cache-emergency', function() {
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    
    // Clear opcache if available
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    
    // Delete all compiled views manually
    $viewPath = storage_path('framework/views');
    $files = glob($viewPath . '/*.php');
    foreach($files as $file) {
        if(is_file($file)) {
            @unlink($file);
        }
    }
    
    return 'All caches cleared successfully! Opcache: ' . (function_exists('opcache_reset') ? 'Cleared' : 'Not available');
});

Route::prefix('admin')->name('admin.')->middleware([\App\Http\Middleware\LogAdminActivity::class, 'track.admin.online'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth:admin');
    Route::get('logs', [LogsController::class, 'index'])->name('logs.index')->middleware(['auth:admin','role:super-admin']);
    Route::delete('logs/clear', [LogsController::class, 'destroyAll'])->name('logs.clear')->middleware(['auth:admin','role:super-admin']);

    // Service Routes
    Route::resource('services', ServiceController::class)->middleware('auth:admin');
    Route::resource('service-types', ServiceTypeController::class)->middleware('auth:admin');
    // Delete a service photo by photo id (no service id needed)
    // Note: we're inside the 'admin.' route name prefix group, so name this route 'services.destroyPhoto'
    // to produce the final name 'admin.services.destroyPhoto' expected by the views.
    Route::delete('services/photos/{photo}', [ServiceController::class, 'destroyPhoto'])->name('services.destroyPhoto')->middleware('auth:admin');
    Route::resource('service-categories', ServiceCategoryController::class)->middleware('auth:admin');
    Route::resource('areas', AreaController::class)->middleware('auth:admin');
    Route::resource('your-items', YourItemsController::class)->middleware('auth:admin');
    // Define specific routes BEFORE the resource route to avoid shadowing by bookings/{booking}
    Route::get('bookings/trashed', [BookingController::class, 'trashed'])->name('bookings.trashed');
    Route::get('bookings/export', [BookingController::class, 'export'])->name('bookings.export')->middleware(['auth:admin','permission:bookings.export']);
    Route::post('bookings/{id}/restore', [BookingController::class, 'restore'])->whereNumber('id')->name('bookings.restore');
    Route::get('bookings/search', [BookingController::class, 'search'])->name('bookings.search');
    Route::resource('bookings', BookingController::class)->middleware(['auth:admin','permission:manage bookings'])->only(['index', 'show', 'destroy', 'update']);
    // Add edit route for bookings so admins can update any fields
    Route::get('bookings/{booking}/edit', [BookingController::class, 'edit'])->name('bookings.edit')->middleware(['auth:admin','permission:manage bookings']);
    Route::get('bookings/{booking}/notify', [BookingController::class, 'notifyForm'])->name('bookings.notify')->middleware(['auth:admin','permission:manage bookings']);
    Route::post('bookings/{booking}/notify', [BookingController::class, 'sendCustomNotification'])->name('bookings.notify.send')->middleware(['auth:admin','permission:manage bookings']);
    // Admin actions for lab assignment and lifecycle
    Route::post('bookings/{booking}/assign-lab', [BookingController::class, 'assignLab'])->name('bookings.assignLab')->middleware(['auth:admin','permission:assign lab']);
    Route::post('bookings/{booking}/arrived-at-lab', [BookingController::class, 'markArrivedAtLab'])->name('bookings.arrivedAtLab')->middleware(['auth:admin','permission:manage bookings']);
    Route::post('bookings/{booking}/picked-from-lab', [BookingController::class, 'markPickedFromLab'])->name('bookings.pickedFromLab')->middleware(['auth:admin','permission:manage bookings']);
    Route::post('bookings/{booking}/driver-collected', [BookingController::class, 'markDriverCollected'])->name('bookings.driverCollected')->middleware(['auth:admin','permission:manage bookings']);
    Route::post('bookings/{booking}/returned-to-user', [BookingController::class, 'markReturnedToUser'])->name('bookings.returnedToUser')->middleware(['auth:admin','permission:manage bookings']);
    // Assign driver to booking (admin)
    Route::post('bookings/{booking}/assign-driver', [BookingController::class, 'assignDriver'])->name('bookings.assignDriver')->middleware(['auth:admin','permission:assign driver']);
    // Assign a car to a booking (car-wash)
    Route::post('bookings/{booking}/assign-car', [BookingCarAssignmentController::class, 'store'])->name('bookings.assignCar')->middleware(['auth:admin','permission:assign car']);
    Route::resource('home-banners', AdminHomeBannerController::class)->middleware('auth:admin');
    Route::resource('admins', AdminController::class)->middleware(['auth:admin','role:super-admin','update.admin.status']);
    // Roles management UI
    Route::resource('roles', \App\Http\Controllers\Dashboard\Admin\RoleController::class)->middleware(['auth:admin','role:super-admin']);
    // AJAX endpoints for role permissions management
    Route::get('roles/{role}/permissions', [\App\Http\Controllers\Dashboard\Admin\RoleController::class, 'permissions'])->name('admin.roles.permissions')->middleware(['auth:admin','role:super-admin']);
    Route::post('roles/{role}/permissions', [\App\Http\Controllers\Dashboard\Admin\RoleController::class, 'updatePermissions'])->name('admin.roles.permissions.update')->middleware(['auth:admin','role:super-admin']);
    Route::resource('carpet-material', CarpetMaterialController::class)->middleware('auth:admin');
    Route::resource('carpet-size', CarpetSizeController::class)->middleware('auth:admin');
    Route::resource('maintenance-or-cleaning', MaintenanceOrCleaningController::class)->middleware('auth:admin');
    Route::resource('size-of-stain', SizeOfStainController::class)->middleware('auth:admin');
    Route::resource('type-of-stain', TypeOfStainController::class)->middleware('auth:admin');
    Route::resource('type-of-service-needed', TypeOfServiceNeededController::class)->middleware('auth:admin');
    Route::resource('level-of-infestation', LevelOfInfestationController::class)->middleware('auth:admin');
    Route::resource('presence-of-children-or-pets', PresenceOfChildrenOrPetsController::class)->middleware('auth:admin');
    Route::resource('payment-methods', PaymentMethodController::class);
    Route::resource('drivers', \App\Http\Controllers\Dashboard\Admin\DriversController::class)->middleware('auth:admin');
    // Assign/remove services to/from drivers
    Route::post('drivers/{driver}/assign-service', [\App\Http\Controllers\Dashboard\Admin\DriversController::class, 'assignService'])->name('drivers.assignService')->middleware('auth:admin');
    Route::delete('drivers/{driver}/remove-service/{service}', [\App\Http\Controllers\Dashboard\Admin\DriversController::class, 'removeService'])->name('drivers.removeService')->middleware('auth:admin');
    Route::resource('cars-additional-service', CarsAdditionalServiceController::class)->middleware('auth:admin');
    Route::resource('place-of-the-cleaning', PlaceOfTheCleaningController::class);
    Route::resource('packages-optional', PackagesOptionalController::class)->middleware('auth:admin');
    Route::resource('number-of-cleaners', NumberOfCleanersController::class)->middleware('auth:admin');
    Route::resource('estimated-hours', EstimatedHoursController::class)->middleware('auth:admin');
    Route::resource('settings', SettingsController::class)->middleware(['auth:admin','role:super-admin']);
    // Render area price table partial (used by admin JS modal)
    Route::get('partials/area-prices', function(Request $request) {
        $base = floatval($request->query('base', 0));
        return view('dashboard.partials.area_price_table', ['basePrice' => $base])->render();
    })->name('admin.partials.area_prices')->middleware('auth:admin');
    // Provide car timeline data as JSON for sidebar (next 48 hours)
    Route::get('partials/car-timeline-data', [CarTimelineController::class, 'timelineData'])->name('admin.partials.car_timeline_data')->middleware('auth:admin');

    // Full-page vehicle timeline view
    Route::get('vehicle-timeline', [CarTimelineController::class, 'fullTimeline'])->name('vehicle-timeline.full')->middleware('auth:admin');
    // Export vehicle timeline as Excel
    Route::get('vehicle-timeline/export', [CarTimelineController::class, 'export'])->name('vehicle-timeline.export')->middleware('auth:admin');

    // Endpoint for admin clients to register their FCM token (called on login)
    Route::post('fcm-token', [FcmTokenController::class, 'store'])->name('fcm-token.store')->middleware('auth:admin');
    
    // Debug route to check admin FCM token status
    Route::get('fcm-token/debug', [FcmTokenController::class, 'debug'])->name('fcm-token.debug')->middleware('auth:admin');

    // Simple API endpoint for unseen notifications (polling fallback)
    Route::get('notifications/unseen', [NotificationApiController::class, 'unseen'])->name('admin.notifications.unseen')->middleware('auth:admin');
    // Emit a short-lived broadcast payload (no queues/Firebase) to trigger dashboard alerts
    Route::post('notifications/emit', [NotificationApiController::class, 'emit'])->name('admin.notifications.emit')->middleware('auth:admin');
    // Mark bookings as seen (optional booking_id in body)
    Route::post('notifications/mark-seen', [NotificationApiController::class, 'markSeen'])->name('admin.notifications.markSeen')->middleware('auth:admin');

    // Admin notifications listing (UI)
    Route::get('notifications/all', [\App\Http\Controllers\Dashboard\Admin\NotificationsController::class, 'index'])->name('notifications.index')->middleware('auth:admin');

    // Toggle read/unread for a notification
    Route::post('notifications/{notification}/toggle-read', [\App\Http\Controllers\Dashboard\Admin\NotificationsController::class, 'toggleRead'])->name('notifications.toggle')->middleware('auth:admin');
    Route::post('notifications/mark-all-read', [\App\Http\Controllers\Dashboard\Admin\NotificationsController::class, 'markAllRead'])->name('notifications.markAll')->middleware('auth:admin');

    // Admin users listing and user bookings
    Route::get('users', [\App\Http\Controllers\Dashboard\Admin\UsersController::class, 'index'])->name('users.index')->middleware('auth:admin');
    Route::get('users/export', [\App\Http\Controllers\Dashboard\Admin\UsersController::class, 'export'])->name('users.export')->middleware(['auth:admin','permission:users.export']);
    Route::get('users/{user}/bookings', [\App\Http\Controllers\Dashboard\Admin\UsersController::class, 'bookings'])->name('users.bookings')->middleware('auth:admin');
    // User profile, edit and update
    Route::get('users/{user}', [\App\Http\Controllers\Dashboard\Admin\UsersController::class, 'show'])->name('users.show')->middleware(['auth:admin','permission:users.view']);
    Route::get('users/{user}/edit', [\App\Http\Controllers\Dashboard\Admin\UsersController::class, 'edit'])->name('users.edit')->middleware(['auth:admin','permission:manage users']);
    Route::put('users/{user}', [\App\Http\Controllers\Dashboard\Admin\UsersController::class, 'update'])->name('users.update')->middleware(['auth:admin','permission:manage users']);
    // Download user's unique code as PDF
    Route::get('users/{user}/code-pdf', [\App\Http\Controllers\Dashboard\Admin\UsersController::class, 'codePdf'])->name('users.code_pdf')->middleware(['auth:admin','permission:users.view']);

    // Booking JSON endpoint for modal details
    Route::get('bookings/{booking}/json', [CarTimelineController::class, 'bookingJson'])->name('admin.bookings.json')->middleware('auth:admin');
    // Invoice PDF generation (supports Arabic/RTL). Accepts optional ?status=new_status to include old/new statuses in invoice.
    Route::get('bookings/{booking}/invoice', [BookingController::class, 'invoicePdf'])->name('bookings.invoice')->middleware(['auth:admin','permission:manage bookings']);
    // Labs management
    Route::resource('labs', \App\Http\Controllers\Dashboard\Admin\LabsController::class)->middleware('auth:admin');
    Route::resource('type-of-package', TypeOfPackageController::class)->middleware('auth:admin');
    Route::resource('frequency', FrequencyController::class)->middleware('auth:admin');
    Route::resource('measurement', MeasurementController::class)->middleware('auth:admin');
    Route::resource('fabric-type', FabricTypeController::class)->middleware('auth:admin');
    // Car wash drivers (Car Wash feature)
    Route::get('car-wash-drivers', [\App\Http\Controllers\Dashboard\Admin\CarWashDriverController::class, 'index'])->name('car-wash-drivers.index')->middleware('auth:admin');
    Route::get('car-wash-drivers/create', [\App\Http\Controllers\Dashboard\Admin\CarWashDriverController::class, 'create'])->name('car-wash-drivers.create')->middleware('auth:admin');
    Route::post('car-wash-drivers', [\App\Http\Controllers\Dashboard\Admin\CarWashDriverController::class, 'store'])->name('car-wash-drivers.store')->middleware('auth:admin');
    // show single driver
    Route::get('car-wash-drivers/{car_wash_driver}', [\App\Http\Controllers\Dashboard\Admin\CarWashDriverController::class, 'show'])->name('car-wash-drivers.show')->middleware('auth:admin');
    Route::get('car-wash-drivers/{car_wash_driver}/edit', [\App\Http\Controllers\Dashboard\Admin\CarWashDriverController::class, 'edit'])->name('car-wash-drivers.edit')->middleware('auth:admin');
    Route::put('car-wash-drivers/{car_wash_driver}', [\App\Http\Controllers\Dashboard\Admin\CarWashDriverController::class, 'update'])->name('car-wash-drivers.update')->middleware('auth:admin');
    Route::delete('car-wash-drivers/{car_wash_driver}', [\App\Http\Controllers\Dashboard\Admin\CarWashDriverController::class, 'destroy'])->name('car-wash-drivers.destroy')->middleware('auth:admin');

    // Driver vehicles admin (index/show)
    Route::get('driver-vehicles', [\App\Http\Controllers\Dashboard\Admin\DriverVehicleController::class, 'index'])->name('driver-vehicles.index')->middleware('auth:admin');
    Route::get('driver-vehicles/create', [\App\Http\Controllers\Dashboard\Admin\DriverVehicleController::class, 'create'])->name('driver-vehicles.create')->middleware('auth:admin');
    Route::post('driver-vehicles', [\App\Http\Controllers\Dashboard\Admin\DriverVehicleController::class, 'store'])->name('driver-vehicles.store')->middleware('auth:admin');
    Route::get('driver-vehicles/{driver_vehicle}', [\App\Http\Controllers\Dashboard\Admin\DriverVehicleController::class, 'show'])->name('driver-vehicles.show')->middleware('auth:admin');
    Route::get('driver-vehicles/{driver_vehicle}/edit', [\App\Http\Controllers\Dashboard\Admin\DriverVehicleController::class, 'edit'])->name('driver-vehicles.edit')->middleware('auth:admin');
    Route::put('driver-vehicles/{driver_vehicle}', [\App\Http\Controllers\Dashboard\Admin\DriverVehicleController::class, 'update'])->name('driver-vehicles.update')->middleware('auth:admin');
    Route::delete('driver-vehicles/{driver_vehicle}', [\App\Http\Controllers\Dashboard\Admin\DriverVehicleController::class, 'destroy'])->name('driver-vehicles.destroy')->middleware('auth:admin');

});
