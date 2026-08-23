<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'subdomain',
        'database',
        'plan',
        'status',
        'trial_ends_at',
        'settings',
        'features',
        'max_drivers',
        'max_jobs_per_month',
        'logo_path',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'settings' => 'array',
        'features' => 'array',
        'max_drivers' => 'integer',
        'max_jobs_per_month' => 'integer',
    ];

    protected $attributes = [
        'settings' => '{}',
        'features' => '{}',
        'logo_path' => null,
    ];

    /**
     * Relationships
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    public function driverProfiles(): HasMany
    {
        return $this->hasMany(DriverProfile::class);
    }

    /**
     * Check if tenant is on trial
     */
    public function isOnTrial(): bool
    {
        return $this->status === 'trial' &&
               $this->trial_ends_at &&
               $this->trial_ends_at->isFuture();
    }

    /**
     * Check if tenant is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' || $this->isOnTrial();
    }

    /**
     * Check if tenant can create more drivers
     */
    public function canAddDriver(): bool
    {
        if ($this->plan === 'enterprise') {
            return true; // Unlimited for enterprise
        }

        $currentDrivers = $this->driverProfiles()->count();
        return $currentDrivers < $this->max_drivers;
    }

    /**
     * Get remaining driver slots
     */
    public function remainingDriverSlots(): int
    {
        if ($this->plan === 'enterprise') {
            return PHP_INT_MAX; // Unlimited
        }

        $currentDrivers = $this->driverProfiles()->count();
        return max(0, $this->max_drivers - $currentDrivers);
    }

    /**
     * Check if tenant can create more jobs this month
     */
    public function canCreateJob(): bool
    {
        if ($this->plan === 'professional' || $this->plan === 'enterprise') {
            return true; // Unlimited for pro and enterprise
        }

        $jobsThisMonth = $this->deliveries()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        return $jobsThisMonth < $this->max_jobs_per_month;
    }

    /**
     * Get jobs created this month
     */
    public function jobsThisMonth(): int
    {
        return $this->deliveries()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
    }

    /**
     * Get remaining job slots for this month
     */
    public function remainingJobSlots(): int
    {
        if ($this->plan === 'professional' || $this->plan === 'enterprise') {
            return PHP_INT_MAX; // Unlimited
        }

        $jobsThisMonth = $this->jobsThisMonth();
        return max(0, $this->max_jobs_per_month - $jobsThisMonth);
    }

    /**
     * Get plan features
     */
    public function getPlanFeatures(): array
    {
        $plans = [
            'starter' => [
                'name' => 'Starter',
                'price' => 99,
                'drivers' => 5,
                'jobs_per_month' => 500,
                'features' => [
                    'Basic tracking',
                    'Mobile app',
                    'Email notifications',
                    'Basic reports',
                ],
            ],
            'professional' => [
                'name' => 'Professional',
                'price' => 299,
                'drivers' => 25,
                'jobs_per_month' => 'unlimited',
                'features' => [
                    'All Starter features',
                    'GPS tracking',
                    'Advanced analytics',
                    'Custom reports',
                    'Priority support',
                ],
            ],
            'enterprise' => [
                'name' => 'Enterprise',
                'price' => 999,
                'drivers' => 'unlimited',
                'jobs_per_month' => 'unlimited',
                'features' => [
                    'All Professional features',
                    'API access',
                    'Custom integrations',
                    'White-label option',
                    'Dedicated support',
                    'Custom domain',
                ],
            ],
        ];

        return $plans[$this->plan] ?? $plans['starter'];
    }

    /**
     * Scope to get active tenants only
     */
    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->where('status', 'active')
              ->orWhere(function($q2) {
                  $q2->where('status', 'trial')
                     ->where('trial_ends_at', '>', now());
              });
        });
    }
}
