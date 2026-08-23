<?php

namespace App\Events;

use App\Models\Delivery;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliveryStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $deliveryId;
    public $status;
    public $created_by;

    public function __construct(Delivery $delivery)
    {
        $this->deliveryId = $delivery->id;
        $this->status = $delivery->status;
        $this->created_by = $delivery->created_by;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('deliveries'),
        ];
    }
    
    public function broadcastWith(): array
    {
        return [
            'delivery_id' => $this->deliveryId,
            'status' => $this->status,
            'created_by' => $this->created_by
        ];
    }
}