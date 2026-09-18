<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hospital extends Model
{
    use SoftDeletes;
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

    public function items()
    {
        return $this->hasMany(DeliveryItem::class, 'hospital_id', 'id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
