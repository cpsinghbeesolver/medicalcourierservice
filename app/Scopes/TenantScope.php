<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Get current tenant from authenticated user
        if (auth()->check() && auth()->user()->tenant_id) {
            $builder->where($model->getTable() . '.tenant_id', auth()->user()->tenant_id);
            return;
        }

        // Get current tenant from app context (set by middleware)
        if (app()->has('tenant')) {
            $builder->where($model->getTable() . '.tenant_id', app('tenant')->id);
            return;
        }

        // If no tenant context, don't filter (for system-level operations)
        // This allows admin operations without tenant scope
    }

    /**
     * Extend the query builder with the scope methods.
     */
    public function extend(Builder $builder): void
    {
        $builder->macro('withoutTenant', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });

        $builder->macro('forTenant', function (Builder $builder, $tenantId) {
            return $builder->withoutGlobalScope($this)
                          ->where('tenant_id', $tenantId);
        });
    }
}
