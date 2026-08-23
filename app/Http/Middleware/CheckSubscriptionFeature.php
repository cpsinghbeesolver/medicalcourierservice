<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $featureKey): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated',
            ], 401);
        }

        // Check if user has an active subscription
        if (!$user->subscription || !$user->subscription->isActive()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active subscription found',
                'code' => 'NO_SUBSCRIPTION',
            ], 403);
        }

        // Check if subscription has the required feature
        if (!$user->subscription->hasFeature($featureKey)) {
            return response()->json([
                'status' => 'error',
                'message' => 'This feature is not available in your current plan',
                'feature' => $featureKey,
                'current_plan' => $user->subscription->plan_display_name,
                'code' => 'FEATURE_NOT_AVAILABLE',
            ], 403);
        }

        return $next($request);
    }
}
