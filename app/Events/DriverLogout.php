<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DriverLogout implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $device_token;

    public function __construct($device_token)
    {
        Log::emergency('logout broadcast '.$device_token);
        $this->device_token = $device_token;
    }

    public function broadcastOn()
    {
        return new PrivateChannel(
            'device-logout'
        );
    }

    public function broadcastAs()
    {
        return 'device.logout';
    }

    public function broadcastWith()
    {
        return [
            'device_token' => $this->device_token
        ];
    }
}
