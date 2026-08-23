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
        Schema::table('delivery_items', function (Blueprint $table) {
            // Drop the existing columns
            $table->dropColumn(['temperature_requirement', 'specimen_type']);
        });

        Schema::table('delivery_items', function (Blueprint $table) {
            // Recreate them as integers
            $table->unsignedInteger('temperature_requirement')->after('description')->nullable();
            $table->unsignedInteger('specimen_type')->after('item_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_items', function (Blueprint $table) {
            $table->dropColumn(['temperature_requirement', 'specimen_type']);
        });

        Schema::table('delivery_items', function (Blueprint $table) {
            $table->enum('temperature_requirement', [
                'frozen',
                'refrigerated',
                'room_temperature'
            ])->after('quantity');

            $table->enum('specimen_type', [
                'blood',
                'urine',
                'tissue',
                'saliva'
            ])->after('temperature_requirement');
        });
    }
};
