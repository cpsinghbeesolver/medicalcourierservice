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
        Schema::table('vehicle_requirements', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });
        Schema::table('vehicle_requirements', function (Blueprint $table) {
            $table->longText('name')->change();
        });

        Schema::table('temperature_requirements', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });
        Schema::table('temperature_requirements', function (Blueprint $table) {
            $table->longText('name')->change();
        });

        Schema::table('specimen_types', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });
        Schema::table('specimen_types', function (Blueprint $table) {
            $table->longText('name')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_requirements', function (Blueprint $table) {
            $table->string('name', 500)->change();
        });
        Schema::table('temperature_requirements', function (Blueprint $table) {
            $table->string('name', 500)->change();
        });
        Schema::table('specimen_types', function (Blueprint $table) {
            $table->string('name', 500)->change();
        });
    }
};
