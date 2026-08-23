<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionUsage extends Model
{
    use HasFactory;

    protected $table = 'subscription_usage';

    protected $fillable = [
        'subscription_id',
        'metric',
        'value',
        'period_date',
    ];

    protected $casts = [
        'period_date' => 'date',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
