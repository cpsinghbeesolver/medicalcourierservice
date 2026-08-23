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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Company name
            $table->string('subdomain')->unique(); // acme-courier
            $table->string('database')->nullable(); // For separate DB (future)
            $table->enum('plan', ['starter', 'professional', 'enterprise'])->default('starter');
            $table->enum('status', ['active', 'trial', 'suspended', 'cancelled'])->default('trial');
            $table->timestamp('trial_ends_at')->nullable();
            $table->json('settings')->nullable(); // Branding, features, limits
            $table->json('features')->nullable(); // Enabled features per plan
            $table->integer('max_drivers')->default(5); // Plan limit
            $table->integer('max_jobs_per_month')->default(500); // Plan limit
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('subdomain');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
