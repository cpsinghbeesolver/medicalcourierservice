<?php

namespace App\Listeners;

use Laravel\Reverb\Events\MessageReceived;
use Illuminate\Support\Facades\Log;

class CaptureMobileLocationTrigger
{
    public function handle(MessageReceived $event): void
    {
        // Decode the raw websocket message framing
        $payload = json_decode($event->message, true);

        // Check if the event name matches your mobile app client-trigger
        if (isset($payload['event']) && $payload['event'] === 'client-driver-location-updated') {
            
            // Reverb wraps client event data as a nested JSON string
            $data = json_decode($payload['data'], true);
            $channel = $payload['channel'] ?? '';

            Log::info('Successfully caught React Native direct trigger!', [
                'channel' => $channel,
                'data' => $data
            ]);

            // Optional: Process coordinates here! 
            // Save to database, or fire a clean server broadcast out to tracking dashboards:
            // event(new ServerTrackingBroadcast($data));
        }
    }
}
