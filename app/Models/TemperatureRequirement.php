<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\EncryptsPhiData;

class TemperatureRequirement extends Model
{
    use EncryptsPhiData,SoftDeletes;
    /**
     * PHI fields that should be encrypted at rest (HIPAA compliance)
     */
    protected $encryptedPhiFields = [
        'name'
    ];

    protected $fillable = [
        'name',
        'status',
        'company_id',
    ];
}