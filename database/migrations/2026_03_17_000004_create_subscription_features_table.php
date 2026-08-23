<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->string('feature_key'); // 'live_gps', 'custom_reports', etc.
            $table->boolean('is_enabled')->default(true);
            $table->integer('limit_value')->nullable(); // For usage limits
            $table->text('metadata')->nullable(); // JSON for additional data
            $table->timestamps();

            $table->unique(['subscription_id', 'feature_key']);
            $table->index(['feature_key', 'is_enabled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_features');
    }
};
