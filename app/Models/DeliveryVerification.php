<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_id',
        'verification_type',
        'recipient_name',
        'signature_image',
        'photo_proof',
        'verified_at',
        'latitude',
        'longitude',
        'notes',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }
}
