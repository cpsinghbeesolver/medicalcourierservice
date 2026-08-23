<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    protected $fillable = [
        'user_id',
        'device_type',
        'fcm_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }
}
