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
        Schema::table('subscription_plans_reference', function (Blueprint $table) {
            $table->integer('min_drivers')->nullable()->after('price_annual');
            $table->integer('max_drivers')->nullable()->after('min_drivers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_plans_reference', function (Blueprint $table) {
            $table->integer('min_drivers')->nullable()->after('price_annual');
            $table->integer('max_drivers')->nullable()->after('min_drivers');
        });
    }
};
