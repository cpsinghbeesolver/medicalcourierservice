<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the host from request
        $host = $request->getHost();

        // Extract subdomain
        // Example: acme-courier.reliatrack.com -> acme-courier
        $parts = explode('.', $host);
        $subdomain = $parts[0];

        // Skip for main domains or common prefixes
        $skipSubdomains = ['www', 'app', 'api', 'localhost', '127'];
        if (in_array($subdomain, $skipSubdomains)) {
            return $next($request);
        }

        // Skip if it's a direct IP address
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return $next($request);
        }

        // Find tenant by subdomain (with caching)
        $tenant = Cache::remember("tenant.{$subdomain}", 3600, function () use ($subdomain) {
            return Tenant::where('subdomain', $subdomain)->first();
        });

        // If no tenant found, return 404
        if (!$tenant) {
            abort(404, 'Tenant not found. Please check your subdomain.');
        }

        // Check tenant status
        if ($tenant->status === 'suspended') {
            abort(403, 'This account has been suspended. Please contact support.');
        }

        if ($tenant->status === 'cancelled') {
            abort(403, 'This account has been cancelled.');
        }

        // Check if trial has expired
        if ($tenant->status === 'trial' && $tenant->trial_ends_at && $tenant->trial_ends_at->isPast()) {
            abort(403, 'Your trial period has ended. Please upgrade to continue using the service.');
        }

        // Set current tenant in app context
        app()->instance('tenant', $tenant);

        // Also set it in request for easy access
        $request->attributes->set('tenant', $tenant);

        // Set tenant in config for easy access
        config(['app.current_tenant' => $tenant]);

        return $next($request);
    }
}
