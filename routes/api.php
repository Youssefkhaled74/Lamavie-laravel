<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\ProfileController;
use App\Http\Controllers\Api\Booking\BookingController;
use App\Http\Controllers\Api\Service\ServiceController;
use App\Http\Controllers\Api\Settings\SettingsController;
use App\Http\Controllers\Api\Frequency\FrequencyController;
use App\Http\Controllers\Api\CarType\CarTypeController;
use App\Http\Controllers\Api\Search\GlobalSearchController;
use App\Http\Controllers\Api\YourItems\YourItemsController;
use App\Http\Controllers\Api\CarpetSize\CarpetSizeController;
use App\Http\Controllers\Api\FabricType\FabricTypeController;
use App\Http\Controllers\Api\Measurement\MeasurementController;
use App\Http\Controllers\Api\ServiceType\ServiceTypeController;
use App\Http\Controllers\Api\SizeOfStain\SizeOfStainController;
use App\Http\Controllers\Api\TypeOfStain\TypeOfStainController;
use App\Http\Controllers\Api\PaymentMethod\PaymentMethodController;
use App\Http\Controllers\Api\TypeOfPackage\TypeOfPackageController;
use App\Http\Controllers\Api\CarpetMaterial\CarpetMaterialController;
use App\Http\Controllers\Api\EstimatedHours\EstimatedHoursController;
use App\Http\Controllers\Api\ServiceCategory\ServiceCategoryController;
use App\Http\Controllers\Api\NumberOfCleaners\NumberOfCleanersController;
use App\Http\Controllers\Api\PackagesOptional\PackagesOptionalController;
use App\Http\Controllers\Api\LevelOfInfestation\LevelOfInfestationController;
use App\Http\Controllers\Api\PlaceOfTheCleaning\PlaceOfTheCleaningController;
use App\Http\Controllers\Api\TypeOfServiceNeeded\TypeOfServiceNeededController;
use App\Http\Controllers\Api\CarsAdditionalService\CarsAdditionalServiceController;
use App\Http\Controllers\Api\MaintenanceOrCleaning\MaintenanceOrCleaningController;
use App\Http\Controllers\Api\Notification\NotificationController;
use App\Http\Controllers\Api\Service\HomeBannerController as ApiHomeBannerController;
use App\Http\Controllers\Api\PresenceOfChildrenOrPets\PresenceOfChildrenOrPetsController;
use App\Http\Controllers\Api\Area\AreaController;
use Illuminate\Support\Facades\Notification;

Route::prefix('user')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
        // Password reset flow
        Route::post('forget-password', [AuthController::class, 'forgetPassword']); // sends otp
        Route::post('verify-password-otp', [AuthController::class, 'verifyPasswordResetOtp']); // verify otp only
        Route::post('set-new-password', [AuthController::class, 'setNewPassword']); // submit new password + otp
        Route::post('resend-otp', [AuthController::class, 'resendOtp']); // resend otp
        Route::middleware('auth.api')->post('logout', [AuthController::class, 'logout']);
        Route::middleware('auth.api')->post('update-fcm-token', [AuthController::class, 'updateFcmToken']);
        Route::middleware('auth.api')->post('update-language', [AuthController::class, 'updateLanguage']);
        Route::middleware('auth.api')->post('profile/update-password', [AuthController::class, 'updatePassword']);
    });
    Route::middleware('auth.api')->prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'getProfile']);
        Route::post('/update', [ProfileController::class, 'updateProfile']);
        // Route::post('/update-email', [ProfileController::class, 'updateEmail']);
        Route::post('/update-phone', [ProfileController::class, 'updatePhone']);
        Route::post('/verify-new-phone', [ProfileController::class, 'verifyNewPhone']); // Add this
        Route::get('/points', [ProfileController::class, 'getPoints']);
        Route::get('/bookings', [ProfileController::class, 'getBookings']);
        Route::get('/notifications', [NotificationController::class, 'getNotifications']);
    });
});

Route::prefix('service')->group(function () {
    Route::get('/', [ServiceController::class, 'index']);
    Route::get('/{id}', [ServiceController::class, 'show']);
    Route::get('/banner/list', [ApiHomeBannerController::class, 'index']);
});

Route::prefix('service-type')->group(function () {
    Route::get('{serviceId}', [ServiceTypeController::class, 'index']);
});

Route::prefix('service-category')->group(function () {
    Route::get('{serviceId}', [ServiceCategoryController::class, 'index']);
});

Route::prefix('your-items')->group(function () {
    Route::get('{serviceCategoryId}', [YourItemsController::class, 'index']);
});

Route::middleware('auth.api')->prefix('booking')->group(function () {
    Route::post('create', [BookingController::class, 'create']);
    Route::post('cancel/{id}', [BookingController::class, 'cancel']);
});

Route::prefix('carpet-material')->group(function () {
    Route::get('{serviceCategoryId}', [CarpetMaterialController::class, 'index']);
});

Route::prefix('carpet-size')->group(function () {
    Route::get('{serviceCategoryId}', [CarpetSizeController::class, 'index']);
});

Route::prefix('maintenance-or-cleaning')->group(function () {
    Route::get('{serviceCategoryId}', [MaintenanceOrCleaningController::class, 'index']);
});

Route::prefix('size-of-stain')->group(function () {
    Route::get('{serviceCategoryId}', [SizeOfStainController::class, 'index']);
});

Route::prefix('type-of-stain')->group(function () {
    Route::get('{serviceCategoryId}', [TypeOfStainController::class, 'index']);
});

Route::prefix('type-of-service-needed')->group(function () {
    Route::get('{serviceCategoryId}', [TypeOfServiceNeededController::class, 'index']);
});

Route::prefix('level-of-infestation')->group(function () {
    Route::get('{serviceCategoryId}', [LevelOfInfestationController::class, 'index']);
});

Route::prefix('presence-of-children-or-pets')->group(function () {
    Route::get('{serviceCategoryId}', [PresenceOfChildrenOrPetsController::class, 'index']);
});

Route::prefix('cars-additional-service')->group(function () {
    Route::get('/{serviceCategoryId}', [CarsAdditionalServiceController::class, 'index'])->name('api.cars-additional-service.index');
});

Route::prefix('place-of-the-cleaning')->group(function () {
    Route::get('{serviceCategoryId}', [PlaceOfTheCleaningController::class, 'index']);
});

Route::prefix('packages-optional')->group(function () {
    Route::get('{serviceCategoryId}', [PackagesOptionalController::class, 'index']);
});

Route::prefix('number-of-cleaners')->group(function () {
    Route::get('{serviceCategoryId}', [NumberOfCleanersController::class, 'index']);
});

Route::prefix('estimated-hours')->group(function () {
    Route::get('{serviceCategoryId}', [EstimatedHoursController::class, 'index']);
});

Route::prefix('settings')->group(function () {
    Route::get('/', [SettingsController::class, 'index']);
});

// Areas list (localized by middleware SetLocale)
Route::get('areas', [AreaController::class, 'index']);

Route::prefix('type-of-package')->group(function () {
    Route::get('{serviceCategoryId}', [TypeOfPackageController::class, 'index']);
});

Route::prefix('frequency')->group(function () {
    Route::get('{serviceCategoryId}', [FrequencyController::class, 'index']);
});

Route::prefix('car-types')->group(function () {
    Route::get('{serviceCategoryId}', [CarTypeController::class, 'index']);
});

Route::prefix('measurement')->group(function () {
    Route::get('{serviceCategoryId}', [MeasurementController::class, 'index']);
});

Route::prefix('fabric-type')->group(function () {
    Route::get('{serviceCategoryId}', [FabricTypeController::class, 'index']);
});

Route::get('payment-methods', [PaymentMethodController::class, 'index']);

// Global search across services, categories, and types
Route::get('search', [GlobalSearchController::class, 'index']);
