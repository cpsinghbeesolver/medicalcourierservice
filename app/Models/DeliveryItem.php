<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\EncryptsPhiData;

class DeliveryItem extends Model
{
    use HasFactory, EncryptsPhiData;

    /**
     * PHI fields that should be encrypted at rest (HIPAA compliance)
     */
    protected $encryptedPhiFields = [
        'barcode',
        'description',
        'handling_instructions',
        'photo_proof',
        'signature_image',
        'notes',
        'dropoff_name',
        'dropoff_address',
        'dropoff_city',
        'dropoff_state',
        'dropoff_zip',
        'dropoff_phone',
        'dropoff_contact_person',
        'item_name',
        'item_code',
        
        // 'dropoff_latitude',
        // 'dropoff_longitude',
    ];

    protected $fillable = [
        'delivery_id',
        'item_type',
        'item_code',
        'item_name',
        'specimen_type',
        'barcode',
        'quantity',
        'description',
        'temperature_requirement',
        'requires_special_handling',
        'handling_instructions',
        'dropoff_name',
        'dropoff_address',
        'dropoff_city',
        'dropoff_state',
        'dropoff_zip',
        'dropoff_phone',
        'dropoff_contact_person',
        'dropoff_latitude',
        'dropoff_longitude',
        'status',
        'photo_proof',
        'signature_image',
        'notes',
        'hospital_id'
    ];

    protected $casts = [
        'requires_special_handling' => 'boolean',
    ];

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function specimenType(): BelongsTo
    {
        return $this->belongsTo(SpecimenType::class, 'specimen_type');
    }
    public function tempratureRequirement(): BelongsTo
    {
        return $this->belongsTo(TemperatureRequirement::class, 'temperature_requirement');
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class, 'hospital_id');
    }
}
