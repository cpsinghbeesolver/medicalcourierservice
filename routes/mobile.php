<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Mobile\MobileAuthController;
use App\Http\Controllers\Api\Mobile\MobileDeliveryController;
use App\Http\Controllers\Api\Mobile\MobileDriverController;
use App\Http\Controllers\Api\Mobile\MobileSafetyChecklistController;
use App\Http\Controllers\Api\Mobile\MobileJobRequestController;
use App\Http\Controllers\Api\Mobile\MobileDeviceController;
use App\Http\Controllers\Api\Mobile\MobileNotificationController;
use App\Http\Controllers\Api\Mobile\ChatController;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Events\DriverLocationUpdated;


/*
|--------------------------------------------------------------------------
| Mobile API Routes
|--------------------------------------------------------------------------
|
| Mobile-optimized API routes with pagination support
|
*/
// Public routes
Route::prefix('v1')->group(function () {
    Route::post('/login', [MobileAuthController::class, 'login']);
    Route::post('/refresh', [MobileAuthController::class, 'refresh']);
    Route::post('/refresh-expiry', [MobileAuthController::class, 'refreshExpiry']);

    // Password reset routes (using main AuthController for consistency)
    Route::post('/forgot-password', [\App\Http\Controllers\Api\Admin\AuthController::class, 'forgotPassword']);
    Route::post('/password/send-otp', [\App\Http\Controllers\Api\Admin\AuthController::class, 'sendOtp']);
    Route::post('/password/verify-otp', [\App\Http\Controllers\Api\Admin\AuthController::class, 'verifyOtp']);
    Route::post('/password/reset-with-otp', [\App\Http\Controllers\Api\Admin\AuthController::class, 'resetPasswordWithOtp']);
    Route::post('/reset-password', [\App\Http\Controllers\Api\Admin\AuthController::class, 'resetPassword']);
});

// Protected routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [MobileAuthController::class, 'logout']);
    Route::get('/profile', [MobileAuthController::class, 'profile']);
    Route::put('/profile', [MobileAuthController::class, 'updateProfile']);
    Route::post('/change-password', [MobileAuthController::class, 'changePassword']);

    // Deliveries with Pagination
    Route::get('/deliveries', [MobileDeliveryController::class, 'index']);
    Route::get('/deliveries/my-active', [MobileDeliveryController::class, 'myActiveDeliveries']);
    Route::get('/deliveries/history', [MobileDeliveryController::class, 'history']);
    Route::get('/deliveries/{id}', [MobileDeliveryController::class, 'show']);

    // Delivery Actions
    Route::post('/deliveries/{id}/accept', [MobileDeliveryController::class, 'acceptDelivery']);
    Route::post('/deliveries/{id}/start', [MobileDeliveryController::class, 'startDelivery']);
    Route::post('/deliveries/{id}/pickup', [MobileDeliveryController::class, 'confirmPickup']);
    Route::post('/deliveries/{id}/complete', [MobileDeliveryController::class, 'completeDelivery']);
    Route::post('/deliveries/{id}/failed', [MobileDeliveryController::class, 'failedDelivery']);
// Save Temperature reading
    Route::post('/deliveries/{id}/temperature/update', [MobileDeliveryController::class, 'temperatureUpdate']);

    // Driver Operations
    Route::post('/driver/location', [MobileDriverController::class, 'updateLocation']);
    Route::post('/driver/availability', [MobileDriverController::class, 'updateAvailability']);
    Route::get('/driver/statistics', [MobileDriverController::class, 'statistics']);
    Route::get('/driver/profile', [MobileDriverController::class, 'profile']);
    Route::get('/driver/availability-status', [MobileDriverController::class, 'availabilityStatus']);
    Route::put('/driver/profile', [MobileDriverController::class, 'updateProfile']);
    Route::put('/driver/profile', [MobileDriverController::class, 'updateProfile']);
    
    // Safety Checklist
    Route::get('/safety-checklist/today', [MobileSafetyChecklistController::class, 'today']);
    Route::post('/safety-checklist', [MobileSafetyChecklistController::class, 'store']);
    Route::get('/safety-checklist/history', [MobileSafetyChecklistController::class, 'history']);
    Route::get('/safety-checklist/{id}', [MobileSafetyChecklistController::class, 'show']);

    // Job Requests (Accept/Decline)
    Route::get('/job-requests', [MobileJobRequestController::class, 'index']);
    Route::post('/job-requests/{id}/accept', [MobileJobRequestController::class, 'accept']);
    Route::post('/job-requests/{id}/decline', [MobileJobRequestController::class, 'decline']);
    Route::get('/job-requests/history', [MobileJobRequestController::class, 'history']);

    // Notifications
    Route::post('/send-notification', [MobileNotificationController::class, 'send']);
    Route::get('/get-notification', [MobileNotificationController::class, 'getNotifications']);
    Route::put('/notifications/{id}/read', [MobileNotificationController::class, 'markAsRead']);
    Route::get('/notifications/read-all', [MobileNotificationController::class, 'markAsReadAll']);
    Route::get('/notification/has-unread', [MobileNotificationController::class, 'hasUnread']);

    // chat messages
    Route::post('/chat/conversations', [ChatController::class, 'createConversation']);
    // Get company/driver conversations
    Route::get('/chat/get-conversations', [ChatController::class, 'conversations']);// Get messages
    Route::get(
        '/chat/conversations/{conversation}/messages',
        [ChatController::class, 'messages']
    );
    // Send message
    Route::post(
        '/chat/conversations/{conversation}/send-messages',
        [ChatController::class, 'sendMessage']
    );

    // Mark messages as read
    Route::post(
        '/chat/conversations/{conversation}/read',
        [ChatController::class, 'markAsRead']
    );
});
