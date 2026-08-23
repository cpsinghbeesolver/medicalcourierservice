<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefreshToken extends Model
{
    protected $casts = [
        'expires_at' => 'datetime',
    ];
    
    protected $fillable = [
        'user_id', 'token', 'expires_at', 'revoked'
    ];

    protected $dates = ['expires_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
