<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'action',
        'model_type',
        'model_id',
        'entity_type', // Alias for model_type (HIPAA compatibility)
        'entity_id',   // Alias for model_id (HIPAA compatibility)
        'description',
        'properties',
        'metadata',    // Alias for properties (HIPAA compatibility)
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Set entity_type attribute (maps to model_type)
     */
    public function setEntityTypeAttribute($value)
    {
        $this->attributes['model_type'] = $value;
    }

    /**
     * Get entity_type attribute (maps to model_type)
     */
    public function getEntityTypeAttribute()
    {
        return $this->attributes['model_type'] ?? null;
    }

    /**
     * Set entity_id attribute (maps to model_id)
     */
    public function setEntityIdAttribute($value)
    {
        $this->attributes['model_id'] = $value;
    }

    /**
     * Get entity_id attribute (maps to model_id)
     */
    public function getEntityIdAttribute()
    {
        return $this->attributes['model_id'] ?? null;
    }

    /**
     * Set metadata attribute (maps to properties)
     */
    public function setMetadataAttribute($value)
    {
        $this->attributes['properties'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Get metadata attribute (maps to properties)
     */
    public function getMetadataAttribute()
    {
        return $this->attributes['properties'] ?? null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->morphTo();
    }
}
