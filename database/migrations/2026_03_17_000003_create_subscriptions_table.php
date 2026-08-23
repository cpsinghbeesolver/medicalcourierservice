<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');

            // Stripe identifiers
            $table->string('stripe_id')->unique();
            $table->string('stripe_customer_id')->index();
            $table->string('stripe_price_id')->nullable();
            $table->string('stripe_product_id')->nullable();

            // Plan details
            $table->enum('plan_name', ['starter', 'professional', 'enterprise'])->default('starter');
            $table->string('plan_display_name')->default('Starter Plan');
            $table->decimal('plan_price', 10, 2)->default(0);
            $table->enum('billing_cycle', ['monthly', 'annual'])->default('monthly');

            // Status
            $table->enum('status', ['active', 'cancelled', 'past_due', 'trialing', 'incomplete', 'unpaid'])->default('active');
            $table->timestamp('trial_ends_at')->nullable();

            // Billing dates
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            // Feature limits
            $table->integer('max_deliveries')->nullable(); // NULL = unlimited
            $table->integer('max_users')->nullable();
            $table->integer('max_locations')->nullable();
            $table->integer('data_retention_days')->default(30);

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['stripe_customer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
