<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyHospital extends Model
{
    protected $fillable = [
        'company_id',
        'hospital_id',
    ];
}
