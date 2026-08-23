<?php

namespace App\Jobs;

use App\Services\FirebaseService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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