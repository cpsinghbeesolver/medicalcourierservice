<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    protected $fillable = [
        'name',
        'hospital_id',
        'registration_number',
        'phone',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'latitude',
        'longitude',
        'contact_person',
        'created_by'
    ];
}
