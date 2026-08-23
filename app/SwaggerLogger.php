<?php

namespace App;

use Psr\Log\AbstractLogger;

class SwaggerLogger extends AbstractLogger
{
    public function log($level, $message, array $context = []): void
    {
        // Suppress the "Required @OA\PathItem() not found" warning
        if (str_contains($message, 'Required @OA\PathItem() not found')) {
            return;
        }

        // Log other messages normally (optional)
        // You can uncomment this to see other messages
        // echo "[{$level}] {$message}\n";
    }
}
