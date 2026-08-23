<?php

namespace App\Traits;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    /**
     * Boot the trait
     */
    protected static function bootBelongsToTenant(): void
    {
        // Add global scope to automatically filter by tenant
        static::addGlobalScope(new TenantScope);

        // Automatically set tenant_id when creating
        static::creating(function ($model) {
            if (!$model->tenant_id && auth()->check()) {
                $model->tenant_id = auth()->user()->tenant_id;
            }

            // If still no tenant_id and we have a current tenant in app context
            if (!$model->tenant_id && app()->has('tenant')) {
                $model->tenant_id = app('tenant')->id;
            }
        });
    }

    /**
     * Get the tenant that owns the model
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }

    /**
     * Scope query without tenant scope (for admin access)
     */
    public function scopeWithoutTenantScope($query)
    {
        return $query->withoutGlobalScope(TenantScope::class);
    }
}
