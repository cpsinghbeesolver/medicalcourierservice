<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToTenant;
use App\Traits\EncryptsPhiData;
use App\Services\HipaaAuditLogger;
use App\Events\DeliveryStatusUpdated;

class Delivery extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, EncryptsPhiData;

    /**
     * PHI fields that should be encrypted at rest (HIPAA compliance)
     */
    protected $encryptedPhiFields = [
        'patient_initials',
        'specimen_id',
        'pickup_name',
        'pickup_address',
        'pickup_phone',
        'pickup_contact_person',
        'delivery_name',
        'delivery_address',
        'delivery_phone',
        'delivery_contact_person',
        'special_instructions',
        'notes',
        'temperature_requirement',
        'temperature_reading'
    ];

    protected $fillable = [
        'tenant_id',
        'delivery_number',
        'driver_id',
        'created_by',
        'specimen_id',
        'patient_initials',
        'pickup_name',
        'pickup_address',
        'pickup_city',
        'pickup_state',
        'pickup_zip',
        'pickup_phone',
        'pickup_contact_person',
        'pickup_latitude',
        'pickup_longitude',
        'pickup_scheduled_time',
        'pickup_actual_time',
        'delivery_name',
        'delivery_address',
        'delivery_city',
        'delivery_state',
        'delivery_zip',
        'delivery_phone',
        'delivery_contact_person',
        'delivery_latitude',
        'delivery_longitude',
        'delivery_scheduled_time',
        'delivery_actual_time',
        'scheduled_time_window_start',
        'scheduled_time_window_end',
        'status',
        'priority',
        'urgency_level',
        'special_instructions',
        'notes',
        'distance_km',
        'estimated_duration_minutes',
        'required_vehicle_type',
        'temperature_requirement',
        'container_count',
        'requires_pickup_barcode_scan',
        'requires_pickup_signature',
        'requires_pickup_photo',
        'requires_dropoff_barcode_scan',
        'requires_dropoff_signature',
        'requires_dropoff_photo',
        'requires_recepient_id_scan',
        'dispatched_at',
        'accepted_by_driver_at',
        'temperature_type'
    ];

    protected $casts = [
        'pickup_latitude' => 'decimal:7',
        'pickup_longitude' => 'decimal:7',
        'delivery_latitude' => 'decimal:7',
        'delivery_longitude' => 'decimal:7',
        'pickup_scheduled_time' => 'datetime',
        'pickup_actual_time' => 'datetime',
        'delivery_scheduled_time' => 'datetime',
        'delivery_actual_time' => 'datetime',
        'scheduled_time_window_start' => 'datetime',
        'scheduled_time_window_end' => 'datetime',
        'distance_km' => 'decimal:2',
        'requires_pickup_barcode_scan' => 'boolean',
        'requires_pickup_signature' => 'boolean',
        'requires_pickup_photo' => 'boolean',
        'requires_dropoff_barcode_scan' => 'boolean',
        'requires_dropoff_signature' => 'boolean',
        'requires_dropoff_photo' => 'boolean',
        'requires_recepient_id_scan' => 'boolean',
        'dispatched_at' => 'datetime',
        'accepted_by_driver_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($delivery) {
            if (empty($delivery->delivery_number)) {
                $delivery->delivery_number = 'DLV-' . strtoupper(uniqid());
            }
        });

        // HIPAA audit logging
        static::created(function ($delivery) {
            HipaaAuditLogger::logCreated('delivery', $delivery->id, $delivery->encryptedPhiFields);
        });

        static::updated(function ($delivery) {
            $changedFields = array_keys($delivery->getChanges());
            $phiChanged = array_intersect($changedFields, $delivery->encryptedPhiFields);
            if (!empty($phiChanged)) {
                HipaaAuditLogger::logUpdated('delivery', $delivery->id, $phiChanged);
            }

            if ($delivery->wasChanged('status')) {
                //event(new DeliveryStatusUpdated($delivery));
            }
        });

        static::deleted(function ($delivery) {
            HipaaAuditLogger::logDeleted('delivery', $delivery->id, $delivery->encryptedPhiFields);
        });
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DeliveryItem::class);
    }

    public function vehicleRequirement(): BelongsTo
    {
        return $this->belongsTo(VehicleRequirement::class, 'required_vehicle_type');
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(DeliveryVerification::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeInTransit($query)
    {
        return $query->where('status', 'in_transit');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'delivered');
    }
}
