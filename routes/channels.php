<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ChatConversation;

Broadcast::routes([
    'middleware' => ['web','auth:sanctum']
]);

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('drivers', function ($user) {
    return true;
});

Broadcast::channel('deliveries', function ($user) {
    return true;
});

Broadcast::channel('driver-locations.{company_id}.{driver_id}', function ($user, $company_id, $driver_id) {
    // return (int) $user->id == (int) $company_id;
    // Log::info([
    //     'user_id' => (int) Auth::id(),
    //     'company_id' => (int) $company_id,
    // ]);
    // if((int) Auth::id() == (int) $company_id){
    //     //update driver lat long
    //     return true;
    // }
    Log::emergency('Location updated');
    return true;
});
Broadcast::channel('driver-disconnected.{driver_id}', function ($user, $driver_id) {
    Log::emergency('Driver Disconnected');
    return true;
});
Broadcast::channel('new-delivery-added.{driver_id}', function ($user, $driver_id) {
    return true;
});

Broadcast::channel('chat.{conversation}', function ($user, $conversationId) {

    $conversation = ChatConversation::find($conversationId);

    if (!$conversation) {
        return false;
    }

    return $conversation->company_id === $user->id ||
           $conversation->driver_id === $user->id;
});

Broadcast::channel('notifications', function ($user) {
    return true;
});

Broadcast::channel('driver-logout.{driver_id}', function ($user, $driver_id) {
    return true;
});