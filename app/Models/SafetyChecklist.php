<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToTenant;

class SafetyChecklist extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'driver_id',
        'checklist_date',
        'checklist_type',
        // Pre-Duty Safety Checklist
        'vehicle_exterior_check',
        'vehicle_interior_clean',
        'fuel_level_adequate',
        'brakes_functional',
        'emergency_kit_present',
        'fire_extinguisher_present',
        'vehicle_notes',
        // White Glove & Etiquette
        'uniform_proper',
        'personal_hygiene',
        'professional_appearance',
        'etiquette_notes',
        // HIPAA & Security Compliance
        'hipaa_trained',
        'patient_privacy_understood',
        'phone_lock_enabled',
        'no_unauthorized_access',
        'compliance_notes',
        // Medical Equipment & Supplies Handling
        'cooler_temperature_checked',
        'specimen_containers_secure',
        'ppe_equipment_available',
        'hand_sanitizer_available',
        'equipment_notes',
        'all_checks_passed',
        'completed_at',
        'signature_image',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'checklist_date' => 'date',
        'vehicle_exterior_check' => 'boolean',
        'vehicle_interior_clean' => 'boolean',
        'fuel_level_adequate' => 'boolean',
        'brakes_functional' => 'boolean',
        'emergency_kit_present' => 'boolean',
        'fire_extinguisher_present' => 'boolean',
        'uniform_proper' => 'boolean',
        'personal_hygiene' => 'boolean',
        'professional_appearance' => 'boolean',
        'hipaa_trained' => 'boolean',
        'patient_privacy_understood' => 'boolean',
        'phone_lock_enabled' => 'boolean',
        'no_unauthorized_access' => 'boolean',
        'cooler_temperature_checked' => 'boolean',
        'specimen_containers_secure' => 'boolean',
        'ppe_equipment_available' => 'boolean',
        'hand_sanitizer_available' => 'boolean',
        'all_checks_passed' => 'boolean',
        'completed_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Check if all required items are checked
     */
    public function calculateAllChecksPassed(): bool
    {
        $requiredChecks = [
            // Vehicle checks
            'vehicle_exterior_check',
            'vehicle_interior_clean',
            'fuel_level_adequate',
            'brakes_functional',
            'emergency_kit_present',
            // Etiquette
            'uniform_proper',
            // HIPAA & Security
            'hipaa_trained',
            'patient_privacy_understood',
            // Medical Equipment
            'cooler_temperature_checked',
            'specimen_containers_secure',
            'ppe_equipment_available',
        ];

        foreach ($requiredChecks as $check) {
            if (!$this->$check) {
                return false;
            }
        }

        return true;
    }
}
