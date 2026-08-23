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
        Schema::create('delivery_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained()->onDelete('cascade');
            $table->enum('verification_type', ['pickup', 'delivery']); // pickup or delivery verification
            $table->string('recipient_name');
            $table->text('signature_image')->nullable(); // base64 or file path
            $table->text('photo_proof')->nullable(); // proof of delivery photo
            $table->dateTime('verified_at');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('delivery_id');
            $table->index('verification_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_verifications');
    }
};
