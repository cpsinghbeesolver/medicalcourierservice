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
            // Patient/Specimen Info (PHI Group) - Encrypted
            $table->string('specimen_id')->nullable()->after('delivery_number'); // Accession Number
            $table->string('patient_initials', 10)->nullable()->after('specimen_id');
            $table->enum('urgency_level', ['routine', 'stat', 'life_threatening'])->default('routine')->after('priority');

            // Time Window (instead of single scheduled time)
            $table->dateTime('scheduled_time_window_start')->nullable()->after('pickup_scheduled_time');
            $table->dateTime('scheduled_time_window_end')->nullable()->after('scheduled_time_window_start');

            // Vehicle Requirements
            $table->string('required_vehicle_type')->nullable()->after('special_instructions'); // refrigerated_van, standard_van, etc.

            // Digital Chain of Custody
            $table->boolean('requires_barcode_scan')->default(false)->after('required_vehicle_type');
            $table->boolean('requires_signature_or_photo')->default(true)->after('requires_barcode_scan');

            // Workflow tracking
            $table->timestamp('dispatched_at')->nullable()->after('requires_signature_or_photo');
            $table->timestamp('accepted_by_driver_at')->nullable()->after('dispatched_at');

            // Add indexes
            $table->index('specimen_id');
            $table->index('urgency_level');
            $table->index(['scheduled_time_window_start', 'scheduled_time_window_end'], 'idx_delivery_time_window');
        });

        Schema::table('driver_profiles', function (Blueprint $table) {
            // Add "clocked_in" status for active shift tracking
            $table->boolean('is_clocked_in')->default(false)->after('availability_status');
            $table->timestamp('clocked_in_at')->nullable()->after('is_clocked_in');
            $table->timestamp('last_location_update')->nullable()->after('current_longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropIndex(['specimen_id']);
            $table->dropIndex(['urgency_level']);
            $table->dropIndex('idx_delivery_time_window');

            $table->dropColumn([
                'specimen_id',
                'patient_initials',
                'urgency_level',
                'scheduled_time_window_start',
                'scheduled_time_window_end',
                'required_vehicle_type',
                'requires_barcode_scan',
                'requires_signature_or_photo',
                'dispatched_at',
                'accepted_by_driver_at',
            ]);
        });

        Schema::table('driver_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'is_clocked_in',
                'clocked_in_at',
                'last_location_update',
            ]);
        });
    }
};
