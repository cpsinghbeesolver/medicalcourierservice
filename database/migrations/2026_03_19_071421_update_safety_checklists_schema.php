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
        Schema::table('safety_checklists', function (Blueprint $table) {
            // Add new fields (4 new fields needed)
            $table->boolean('windshield_cleaned')->default(false);
            $table->boolean('vehicle_locked')->default(false);
            $table->boolean('secure_phi_containers')->default(false);
            $table->boolean('extra_leakproof_bags')->default(false);

            // Remove fields that are not in the new schema
            $table->dropColumn([
                'vehicle_exterior_check',
                'vehicle_interior_clean',
                'fuel_level_adequate',
                'brakes_functional',
                'emergency_kit_present',
                'fire_extinguisher_present',
                'vehicle_notes',
                'uniform_proper',
                'personal_hygiene',
                'professional_appearance',
                'etiquette_notes',
                'hipaa_trained',
                'patient_privacy_understood',
                'phone_lock_enabled',
                'no_unauthorized_access',
                'compliance_notes',
                'cooler_temperature_checked',
                'specimen_containers_secure',
                'ppe_equipment_available',
                'hand_sanitizer_available',
                'equipment_notes',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('safety_checklists', function (Blueprint $table) {
            // Remove new fields
            $table->dropColumn([
                'windshield_cleaned',
                'vehicle_locked',
                'secure_phi_containers',
                'extra_leakproof_bags',
            ]);

            // Add back old fields
            $table->boolean('vehicle_exterior_check')->default(false);
            $table->boolean('vehicle_interior_clean')->default(false);
            $table->boolean('fuel_level_adequate')->default(false);
            $table->boolean('brakes_functional')->default(false);
            $table->boolean('emergency_kit_present')->default(false);
            $table->boolean('fire_extinguisher_present')->default(false);
            $table->text('vehicle_notes')->nullable();
            $table->boolean('uniform_proper')->default(false);
            $table->boolean('personal_hygiene')->default(false);
            $table->boolean('professional_appearance')->default(false);
            $table->text('etiquette_notes')->nullable();
            $table->boolean('hipaa_trained')->default(false);
            $table->boolean('patient_privacy_understood')->default(false);
            $table->boolean('phone_lock_enabled')->default(false);
            $table->boolean('no_unauthorized_access')->default(false);
            $table->text('compliance_notes')->nullable();
            $table->boolean('cooler_temperature_checked')->default(false);
            $table->boolean('specimen_containers_secure')->default(false);
            $table->boolean('ppe_equipment_available')->default(false);
            $table->boolean('hand_sanitizer_available')->default(false);
            $table->text('equipment_notes')->nullable();
        });
    }
};
