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

    public $driver_id;

    public function __construct($driver_id)
    {
        Log::emergency('logout broadcast '.$driver_id);
        $this->driver_id = $driver_id;
    }

    public function broadcastOn()
    {
        return new PrivateChannel(
            'driver-logout.' . $this->driver_id
        );
    }

    public function broadcastAs()
    {
        return 'driver.logout';
    }

    public function broadcastWith()
    {
        return [
            'driver_id' => $this->driver_id
        ];
    }
}
