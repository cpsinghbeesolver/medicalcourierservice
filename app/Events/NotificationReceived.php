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
use Illuminate\Support\Facades\Log;

class NotificationReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $company_id;

    public function __construct($company_id)
    {
        Log::emergency('Notification received'.$company_id);
        $this->company_id = $company_id;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications'),
        ];
    }
    
    public function broadcastWith(): array
    {
        return [
            'company_id' => $this->company_id
        ];
    }
}