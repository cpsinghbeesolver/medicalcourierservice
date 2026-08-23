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


class DriverLocationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public int $driver_id;
    public string $driver_name;
    public int $company_id;
    public float $latitude;
    public float $longitude;

    public function __construct($location)
    {
        Log::emergency('Location updated');
        // dd($location);
        $this->company_id = $location['company_id'];
        $this->driver_id = $location['driver_id'];
        $this->driver_name = $location['driver_name'];
        $this->latitude = $location['lat'];
        $this->longitude = $location['long'];
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('driver-locations.' . $this->company_id.'.'.$this->driver_id)];
    }

    public function broadcastAs(): string
    {
        return 'client-driver-location-updated';
    }

    public function broadcastWith(): array
    {
        return [
            'company_id' => $this->company_id,
            'driver_id' => $this->driver_id,
            'driver_name' => $this->driver_name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude
        ];
    }
}
