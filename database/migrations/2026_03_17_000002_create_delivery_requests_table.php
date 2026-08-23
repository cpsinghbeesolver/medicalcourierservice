<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('delivery_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'declined', 'expired'])->default('pending');
            $table->timestamp('requested_at');
            $table->timestamp('responded_at')->nullable();
            $table->text('decline_reason')->nullable();
            $table->string('decline_reason_category')->nullable(); // too_far, time_conflict, vehicle_issue, personal, other
            $table->integer('response_time_seconds')->nullable();
            $table->timestamps();

            $table->index(['driver_id', 'status']);
            $table->index(['delivery_id', 'driver_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_requests');
    }
};
