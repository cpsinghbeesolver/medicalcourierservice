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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_number')->unique();
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            // Pickup Information
            $table->string('pickup_name');
            $table->text('pickup_address');
            $table->string('pickup_city');
            $table->string('pickup_state');
            $table->string('pickup_zip');
            $table->string('pickup_phone');
            $table->string('pickup_contact_person')->nullable();
            $table->decimal('pickup_latitude', 10, 7)->nullable();
            $table->decimal('pickup_longitude', 10, 7)->nullable();
            $table->dateTime('pickup_scheduled_time');
            $table->dateTime('pickup_actual_time')->nullable();

            // Delivery Information
            $table->string('delivery_name');
            $table->text('delivery_address');
            $table->string('delivery_city');
            $table->string('delivery_state');
            $table->string('delivery_zip');
            $table->string('delivery_phone');
            $table->string('delivery_contact_person')->nullable();
            $table->decimal('delivery_latitude', 10, 7)->nullable();
            $table->decimal('delivery_longitude', 10, 7)->nullable();
            $table->dateTime('delivery_scheduled_time');
            $table->dateTime('delivery_actual_time')->nullable();

            // Status and Priority
            $table->enum('status', ['pending', 'assigned', 'in_transit', 'picked_up', 'delivered', 'failed', 'cancelled'])->default('pending');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');

            // Additional Information
            $table->text('special_instructions')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->integer('estimated_duration_minutes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('delivery_number');
            $table->index('driver_id');
            $table->index('status');
            $table->index(['pickup_scheduled_time', 'delivery_scheduled_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
