<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('safety_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->date('checklist_date');
            $table->enum('checklist_type', ['pre_duty', 'post_duty'])->default('pre_duty');

            // Pre-Duty Safety Checklist
            $table->boolean('vehicle_exterior_check')->default(false);
            $table->boolean('vehicle_interior_clean')->default(false);
            $table->boolean('fuel_level_adequate')->default(false);
            $table->boolean('tire_pressure_checked')->default(false);
            $table->boolean('lights_functional')->default(false);
            $table->boolean('brakes_functional')->default(false);
            $table->boolean('emergency_kit_present')->default(false);
            $table->boolean('fire_extinguisher_present')->default(false);
            $table->text('vehicle_notes')->nullable();

            // White Glove & Etiquette
            $table->boolean('uniform_proper')->default(false);
            $table->boolean('id_badge_visible')->default(false);
            $table->boolean('personal_hygiene')->default(false);
            $table->boolean('professional_appearance')->default(false);
            $table->text('etiquette_notes')->nullable();

            // HIPAA & Security Compliance
            $table->boolean('hipaa_trained')->default(false);
            $table->boolean('patient_privacy_understood')->default(false);
            $table->boolean('secure_transport_containers')->default(false);
            $table->boolean('phone_lock_enabled')->default(false);
            $table->boolean('no_unauthorized_access')->default(false);
            $table->text('compliance_notes')->nullable();

            // Medical Equipment & Supplies Handling
            $table->boolean('cooler_temperature_checked')->default(false);
            $table->boolean('biohazard_bags_available')->default(false);
            $table->boolean('specimen_containers_secure')->default(false);
            $table->boolean('ppe_equipment_available')->default(false);
            $table->boolean('hand_sanitizer_available')->default(false);
            $table->boolean('gloves_available')->default(false);
            $table->text('equipment_notes')->nullable();

            $table->boolean('all_checks_passed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->string('signature_image')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->timestamps();

            $table->index(['driver_id', 'checklist_date']);
            $table->index(['tenant_id', 'checklist_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('safety_checklists');
    }
};
