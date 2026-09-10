<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Delivery;

class NewDeliveryAdded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $delivery_id,$driver_id;

    /**
     * Create a new event instance.
     */
    public function __construct($delivery_id,$driver_id)
    {
        $this->delivery_id = $delivery_id;
        $this->driver_id = $driver_id;
        Log::emergency('New delivery added');            
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('new-delivery-added.'. $this->driver_id),
        ];
    }
    
    public function broadcastAs(): string
    {
        return 'new-delivery-added';
    }

    public function broadcastWith(): array
    {
        $delivery = Delivery::with([
            'driver:id,name,phone',
            'creator:id,name',
            'items'
        ])->where('id', $this->delivery_id)->first();

        if ($delivery) {
            return [
                'success' => true,
                'data' => [
                    'id' => $delivery->id,
                    'delivery_number' => $delivery->delivery_number,
                    'status' => $delivery->status,
                    'priority' => $delivery->priority,

                    'pickup' => [
                        'name' => $delivery->pickup_name,
                        'address' => $delivery->pickup_address,
                        'city' => $delivery->pickup_city,
                        'phone' => $delivery->pickup_phone,
                        'scheduled_time' => $delivery->pickup_scheduled_time,
                        'actual_time' => $delivery->pickup_actual_time,
                        'location' => [
                            'latitude' => $delivery->pickup_latitude,
                            'longitude' => $delivery->pickup_longitude,
                        ],
                    ],

                    'delivery' => [
                        'name' => $delivery->delivery_name,
                        'address' => $delivery->delivery_address,
                        'city' => $delivery->delivery_city,
                        'phone' => $delivery->delivery_phone,
                        'scheduled_time' => $delivery->delivery_scheduled_time,
                        'actual_time' => $delivery->delivery_actual_time,
                        'requires_pickup_barcode_scan' => $delivery->requires_pickup_barcode_scan,
                        'requires_pickup_signature' => $delivery->requires_pickup_signature,
                        'requires_pickup_photo' => $delivery->requires_pickup_photo,
                        'requires_recepient_id_scan' => $delivery->requires_recepient_id_scan,
                        'requires_dropoff_signature' => $delivery->requires_dropoff_signature,
                        'requires_dropoff_barcode_scan' => $delivery->requires_dropoff_barcode_scan,
                        'requires_dropoff_photo' => $delivery->requires_dropoff_photo,
                        'location' => [
                            'latitude' => $delivery->delivery_latitude,
                            'longitude' => $delivery->delivery_longitude,
                        ],
                    ],

                    'driver' => $delivery->driver ? [
                        'id' => $delivery->driver->id,
                        'name' => $delivery->driver->name,
                        'phone' => $delivery->driver->phone,
                    ] : null,

                    'item_count' => $delivery->items->count(),
                    'distance_km' => $delivery->distance_km,
                    'estimated_duration_minutes' => $delivery->estimated_duration_minutes,
                    'special_instructions' => $delivery->special_instructions,
                    'created_at' => $delivery->created_at?->toIso8601String(),
                ],
            ];
        }

        return [
            'success' => false,
            'data' => null,
            'message' => 'Delivery not found',
        ];
    }
}
