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
            // Add dropoff location fields for each item
            $table->string('dropoff_name')->nullable()->after('handling_instructions');
            $table->text('dropoff_address')->nullable()->after('dropoff_name');
            $table->string('dropoff_city')->nullable()->after('dropoff_address');
            $table->string('dropoff_state')->nullable()->after('dropoff_city');
            $table->string('dropoff_zip')->nullable()->after('dropoff_state');
            $table->string('dropoff_phone')->nullable()->after('dropoff_zip');
            $table->string('dropoff_contact_person')->nullable()->after('dropoff_phone');
            $table->decimal('dropoff_latitude', 10, 7)->nullable()->after('dropoff_contact_person');
            $table->decimal('dropoff_longitude', 10, 7)->nullable()->after('dropoff_latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_items', function (Blueprint $table) {
            $table->dropColumn([
                'dropoff_name',
                'dropoff_address',
                'dropoff_city',
                'dropoff_state',
                'dropoff_zip',
                'dropoff_phone',
                'dropoff_contact_person',
                'dropoff_latitude',
                'dropoff_longitude'
            ]);
        });
    }
};
