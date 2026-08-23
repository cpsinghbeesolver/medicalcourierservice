<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPlansReference extends Model
{
    use HasFactory;

    protected $table = 'subscription_plans_reference';

    protected $fillable = [
        'name',
        'display_name',
        'price_monthly',
        'min_drivers',
        'max_drivers',
        'data_retention_days',
        'features_json'

    ];

    protected $casts = [
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}