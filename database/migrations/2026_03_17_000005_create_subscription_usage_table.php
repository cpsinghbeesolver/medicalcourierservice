<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->string('metric'); // 'deliveries', 'users', 'api_calls', etc.
            $table->integer('value')->default(0);
            $table->date('period_date'); // For tracking monthly usage
            $table->timestamps();

            $table->unique(['subscription_id', 'metric', 'period_date']);
            $table->index(['subscription_id', 'period_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_usage');
    }
};
