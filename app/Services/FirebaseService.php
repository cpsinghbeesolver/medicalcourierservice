<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use App\Models\Notifications as UserNotification;

class FirebaseService
{
    /**
     * Create a new Notification instance.
     */
    protected $messaging;
    public function __construct()
    {
        $this->messaging = (new Factory)
            ->withServiceAccount(
                storage_path('app/firebase/reliastat-tech-firebase-adminsdk-fbsvc-c139025883.json')
            )
            ->createMessaging();
    }

    public function sendToToken(
        string $token,
        string $title,
        string $body,
        string $type,
        int $user_id,
        array $data = []
    ) {
        // Save to database
        UserNotification::create([
            'user_id' => $user_id,
            'title' => $title,
            'body' => $body,
            'type' => $type,
            'data' => $data,
        ]);

        $message = CloudMessage::new()
        ->toToken($token)
        ->withNotification(
            Notification::create($title, $body)
        )
        ->withData($data);

        return $this->messaging->send($message);
    }
}
