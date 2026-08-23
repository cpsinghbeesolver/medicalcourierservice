<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Company\DeliveryController;
use App\Http\Controllers\Api\Company\DriverController;
use App\Http\Controllers\Api\Company\DriverProfileController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Common\ActivityLogController;
use App\Http\Controllers\Api\Common\ProfileController;
use App\Http\Controllers\Api\Admin\WaitlistController;
use App\Http\Controllers\Api\Common\ContactController;
use App\Http\Controllers\Company\SpecimenTypeController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use App\Events\DriverLocationUpdated;
use App\Http\Controllers\Api\Mobile\MobileNotificationController;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

// Public routes
Route::prefix('v1')->group(function () {
    // Waitlist & Contact Form submissions (Public)
    Route::post('/waitlist', [WaitlistController::class, 'store']);
    Route::post('/contact', [ContactController::class, 'store']);

    // Authentication routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Password reset routes
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']); // Send reset link
    Route::post('/password/send-otp', [AuthController::class, 'sendOtp']); // Send OTP code
    Route::post('/password/verify-otp', [AuthController::class, 'verifyOtp']); // Verify OTP
    Route::post('/reset-password', [AuthController::class, 'resetPassword']); // Reset with link token
    Route::post('/password/reset-with-otp', [AuthController::class, 'resetPasswordWithOtp']); // Reset with OTP

    // Email verification routes
    Route::post('/send-verification-code', [AuthController::class, 'sendVerificationCode']);
    Route::post('/verify-code', [AuthController::class, 'verifyCode']);
    Route::post('/resend-verification-code', [AuthController::class, 'resendVerificationCode']);

    // Stripe webhook
    Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);
});

// Protected routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
// Route::prefix('v1')->middleware('custom.auth')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::match(['put', 'post'], '/profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Profile & Subscription routes
    Route::get('/profile/details', [ProfileController::class, 'show']);
    Route::get('/profile/subscription-usage', [ProfileController::class, 'subscriptionUsage']);
    Route::get('/profile/check-feature/{featureKey}', [ProfileController::class, 'checkFeature']);
    
    // Delivery routes
    Route::get('/locations', [DeliveryController::class, 'getLocations']);
    Route::get('/deliveries', [DeliveryController::class, 'index']);
    // Route::post('/deliveries', [DeliveryController::class, 'store'])->middleware('subscription.limit:deliveries');
    Route::post('/deliveries', [DeliveryController::class, 'store']);
    Route::post('/edit-delivery/{id}', [DeliveryController::class, 'editDelivery']);
    Route::get('/deliveries/{delivery}', [DeliveryController::class, 'show']);
    Route::put('/deliveries/{delivery}', [DeliveryController::class, 'update']);
    Route::delete('/deliveries/{delivery}', [DeliveryController::class, 'destroy']);
    Route::post('/deliveries/{delivery}/assign', [DeliveryController::class, 'assignDriver']);
    Route::post('/deliveries/{delivery}/start', [DeliveryController::class, 'startDelivery']);
    Route::post('/deliveries/{delivery}/pickup', [DeliveryController::class, 'confirmPickup']);
    Route::post('/deliveries/{delivery}/complete', [DeliveryController::class, 'completeDelivery']);
    Route::post('/deliveries/{delivery}/cancel', [DeliveryController::class, 'cancelDelivery']);
    Route::post('/deliveries/{delivery}/resume', [DeliveryController::class, 'resumeDelivery']);
    Route::post('/add-specimen-type', [DeliveryController::class, 'addSpecimenType']);
    Route::post('/add-temperature-requirement', [DeliveryController::class, 'addTemperatureRequirement']);
    Route::post('/add-vehicle-requirement', [DeliveryController::class, 'addVehicleRequirement']);
    Route::get('/deliveries/{delivery}/tracking', [DeliveryController::class, 'tracking'])->middleware('subscription.feature:live_gps');
    Route::get('/deliveries-monthly', [DeliveryController::class, 'monthlyDelivery'])->name('monthlyDelivery');

    // Driver-specific routes
    Route::get('/driver/deliveries', [DriverController::class, 'myDeliveries']);
    Route::get('/driver/deliveries/available', [DriverController::class, 'availableDeliveries']);
    Route::post('/driver/location', [DriverController::class, 'updateLocation'])->middleware('subscription.feature:live_gps');
    Route::post('/driver/availability', [DriverController::class, 'updateAvailability']);
    Route::get('/driver/statistics', [DriverController::class, 'statistics']);
    Route::post('/driver/clock-in', [DriverController::class, 'clockIn']);
    Route::post('/driver/clock-out', [DriverController::class, 'clockOut']);
    Route::get('/driver/clock-status', [DriverController::class, 'clockStatus']);

    // Driver Profile routes
    Route::apiResource('driver-profiles', DriverProfileController::class);

    // User management routes
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store'])->middleware('subscription.limit:users');
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    Route::get('/drivers/available', [UserController::class, 'getAvailableDrivers']);

    // Activity logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index']);
    Route::get('/activity-logs/my', [ActivityLogController::class, 'myLogs']);

    // Waitlist Management (Admin only)
    Route::get('/waitlist', [WaitlistController::class, 'index']);
    Route::get('/waitlist/statistics', [WaitlistController::class, 'statistics']);
    Route::get('/waitlist/{id}', [WaitlistController::class, 'show']);
    Route::put('/waitlist/{id}', [WaitlistController::class, 'update']);
    Route::delete('/waitlist/{id}', [WaitlistController::class, 'destroy']);

    // Contact Submissions Management (Admin only)
    Route::get('/contact', [ContactController::class, 'index']);
    Route::get('/contact/statistics', [ContactController::class, 'statistics']);
    Route::get('/contact/{id}', [ContactController::class, 'show']);
    Route::put('/contact/{id}', [ContactController::class, 'update']);
    Route::post('/contact/{id}/assign', [ContactController::class, 'assign']);
    Route::delete('/contact/{id}', [ContactController::class, 'destroy']);
    Route::get('/search-names', [ProfileController::class, 'searchNames']);

    Route::post('/save-fcm-token', [MobileNotificationController::class, 'saveToken']);
    
});
