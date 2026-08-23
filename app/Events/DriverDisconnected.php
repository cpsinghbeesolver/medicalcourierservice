<?php

namespace App\Events;

use Illuminate\Support\Facades\Redis;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Exception;
use Illuminate\Support\Facades\Log;


class DriverDisconnected implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public int $driver_id;

    public function __construct($location)
    {
        Log::emergency('Driver disconnected');
        // dd($location);
        $this->driver_id = $location['driver_id'];
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('driver-disconnected.' . $this->driver_id)];
    }

    public function broadcastAs(): string
    {
        return 'client-get-driver-disconnected';
    }

    public function broadcastWith(): array
    {
        return [
            'driver_id' => $this->driver_id
        ];
    }
}
