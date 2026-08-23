<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This table is for reference only - actual subscriptions come from Stripe
     */
    public function up(): void
    {
        Schema::create('subscription_plans_reference', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // starter, professional, enterprise
            $table->string('display_name');
            $table->decimal('price_monthly', 10, 2)->nullable();
            $table->decimal('price_annual', 10, 2)->nullable();
            $table->integer('max_deliveries')->nullable(); // NULL = unlimited
            $table->integer('max_users')->nullable();
            $table->integer('max_locations')->nullable();
            $table->integer('data_retention_days')->default(30);
            $table->json('features_json')->nullable(); // Array of feature keys
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans_reference');
    }
};
