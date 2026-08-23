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
            // Increase column sizes for encrypted PHI data
            // Encrypted data is typically 200-300 characters
            $table->string('specimen_id', 500)->nullable()->change();
            $table->string('patient_initials', 500)->nullable()->change();
            $table->string('pickup_name', 500)->change();
            $table->text('pickup_address')->change();
            $table->string('pickup_phone', 500)->change();
            $table->string('pickup_contact_person', 500)->nullable()->change();
            $table->string('delivery_name', 500)->change();
            $table->text('delivery_address')->change();
            $table->string('delivery_phone', 500)->change();
            //$table->string('temperature_requirement', 500)->nullable()->change();
            $table->string('delivery_contact_person', 500)->nullable()->change();
            $table->text('special_instructions')->nullable()->change();
            $table->text('notes')->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            // Increase user PHI fields
            $table->string('name', 500)->change();
            $table->string('phone', 500)->nullable()->change();
        });
        Schema::table('delivery_items', function (Blueprint $table) {
            // Increase user PHI fields
            //$table->string('dropoff_name', 500)->change();
            //$table->string('dropoff_phone', 500)->nullable()->change();
            //$table->string('dropoff_contact_person', 500)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('specimen_id', 255)->nullable()->change();
            $table->string('patient_initials', 10)->nullable()->change();
            $table->string('pickup_name', 255)->change();
            $table->string('pickup_phone', 20)->change();
            $table->string('pickup_contact_person', 255)->nullable()->change();
            $table->string('delivery_name', 255)->change();
            $table->string('delivery_phone', 20)->change();
            $table->string('delivery_contact_person', 255)->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->string('phone', 20)->nullable()->change();
        });
    }
};
