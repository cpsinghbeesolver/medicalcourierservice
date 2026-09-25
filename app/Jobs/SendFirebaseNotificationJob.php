<?php

namespace App\Jobs;

use App\Services\FirebaseService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Events\NotificationReceived;

class SendFirebaseNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $token,
        public string $title,
        public string $body,
        public string $type,
        public int $user_id,
        public array $data = []
    ) {}

    public function handle(FirebaseService $firebaseService): void
    {
        if($this->type == 'web'){
            event(new NotificationReceived($this->user_id));
        }
        $firebaseService->sendToToken(
            $this->token,
            $this->title,
            $this->body,
            $this->type,
            $this->user_id,
            $this->data
        );
        
    }
}