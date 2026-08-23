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
        Schema::table('deliveries', function (Blueprint $table) {
            // Temperature requirement for entire delivery (instead of per-item)
            $table->string('temperature_requirement')->nullable()->after('required_vehicle_type');

            // Number of containers or bags
            $table->integer('container_count')->default(1)->after('temperature_requirement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn(['temperature_requirement', 'container_count']);
        });
    }
};
