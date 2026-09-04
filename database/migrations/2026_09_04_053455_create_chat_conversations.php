<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('driver_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamp('last_message_at')->nullable();

            $table->timestamps();

            // One conversation between a company and driver
            $table->unique(['company_id', 'driver_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_conversations');
    }
};
