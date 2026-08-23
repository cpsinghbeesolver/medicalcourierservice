<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToTenant;

class Subscription extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'user_id',
        'tenant_id',
        'stripe_id',
        'stripe_customer_id',
        'stripe_price_id',
        'stripe_product_id',
        'plan_name',
        'plan_display_name',
        'plan_price',
        'billing_cycle',
        'status',
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
        'cancelled_at',
        'ends_at',
        'max_deliveries',
        'max_users',
        'max_locations',
        'data_retention_days',
    ];

    protected $casts = [
        'plan_price' => 'decimal:2',
        'trial_ends_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'cancelled_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function features(): HasMany
    {
        return $this->hasMany(SubscriptionFeature::class);
    }

    public function usage(): HasMany
    {
        return $this->hasMany(SubscriptionUsage::class);
    }

    /**
     * Check if subscription has a specific feature
     */
    public function hasFeature(string $featureKey): bool
    {
        return $this->features()
            ->where('feature_key', $featureKey)
            ->where('is_enabled', true)
            ->exists();
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' &&
               ($this->ends_at === null || $this->ends_at->isFuture());
    }

    /**
     * Check if subscription is on trial
     */
    public function onTrial(): bool
    {
        return $this->status === 'trialing' &&
               $this->trial_ends_at &&
               $this->trial_ends_at->isFuture();
    }

    /**
     * Check if subscription is cancelled
     */
    public function cancelled(): bool
    {
        return $this->cancelled_at !== null;
    }

    /**
     * Check if delivery limit is reached
     */
    public function deliveryLimitReached(): bool
    {
        if ($this->max_deliveries === null) {
            return false; // Unlimited
        }

        $usage = $this->getCurrentUsage('deliveries');
        return $usage >= $this->max_deliveries;
    }

    /**
     * Get current usage for a metric
     */
    public function getCurrentUsage(string $metric): int
    {
        return $this->usage()
            ->where('metric', $metric)
            ->where('period_date', now()->format('Y-m-d'))
            ->value('value') ?? 0;
    }

    /**
     * Increment usage counter
     */
    public function incrementUsage(string $metric, int $amount = 1): void
    {
        $usage = $this->usage()->firstOrCreate([
            'metric' => $metric,
            'period_date' => now()->format('Y-m-d'),
        ], [
            'value' => 0
        ]);

        $usage->increment('value', $amount);
    }

    /**
     * Scope to active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to cancelled subscriptions
     */
    public function scopeCancelled($query)
    {
        return $query->whereNotNull('cancelled_at');
    }
}
