<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToTenant;
use App\Traits\EncryptsPhiData;
use App\Events\DriverStatusUpdated;

class DriverProfile extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, EncryptsPhiData;

    /**
     * PHI fields that should be encrypted at rest (HIPAA compliance)
     */
    protected $encryptedPhiFields = [
        'license_number',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'hipaa_certification_file',
        'country_code',
        'country_flag',
        'iso_code'
    ];

    protected $fillable = [
        'tenant_id',
        'user_id',
        'created_by',
        'license_number',
        'license_expiry_date',
        'license_state',
        'vehicle_type',
        'vehicle_plate_number',
        'address',
        'city',
        'state',
        'zip_code',
        'date_of_birth',
        'insurance_policy_number',
        'insurance_expiry_date',
        'hipaa_certification_date',
        'hipaa_certification_file',
        'background_check_status',
        'drug_screen_expiry',
        'specimen_handling_certification_date',
        'specimen_handling_confirmed',
        'bloodborne_pathogen_training_date',
        'bloodborne_pathogen_file',
        'emergency_contact_name',
        'emergency_contact_phone',
        'availability_status',
        'current_latitude',
        'current_longitude',
        'is_clocked_in',
        'clocked_in_at',
        'last_location_update',
        'country_code',
        'country_flag',
        'iso_code'
    ];

    protected $casts = [
        'license_expiry_date' => 'date',
        'date_of_birth' => 'date',
        'insurance_expiry_date' => 'date',
        'hipaa_certification_date' => 'date',
        'drug_screen_expiry' => 'date',
        'specimen_handling_certification_date' => 'date',
        'bloodborne_pathogen_training_date' => 'date',
        'specimen_handling_confirmed' => 'boolean',
        'current_latitude' => 'decimal:7',
        'current_longitude' => 'decimal:7',
        'is_clocked_in' => 'boolean',
        'clocked_in_at' => 'datetime',
        'last_location_update' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isLicenseExpired(): bool
    {
        return $this->license_expiry_date && $this->license_expiry_date->isPast();
    }

    public function updateLocation(float $latitude, float $longitude): void
    {
        $this->update([
            'current_latitude' => $latitude,
            'current_longitude' => $longitude,
            'last_location_update' => now(),
        ]);
    }

    public function clockIn(): void
    {
        $this->update([
            'is_clocked_in' => true,
            'clocked_in_at' => now(),
            'availability_status' => 'available',
        ]);
    }

    public function clockOut(): void
    {
        $this->update([
            'is_clocked_in' => false,
            'availability_status' => 'off_duty',
        ]);
    }

    public function isActive(): bool
    {
        return $this->is_clocked_in && $this->availability_status === 'available';
    }
    
    public function company()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::updated(function ($driver) {
            // event(new DriverStatusUpdated($driver));
            if ($driver->isDirty('availability_status')) {
                event(new DriverStatusUpdated($driver));
            }
        });
    }
}
