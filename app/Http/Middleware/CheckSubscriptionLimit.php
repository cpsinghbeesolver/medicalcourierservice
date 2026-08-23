<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $limitType = 'deliveries'): Response
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

        $subscription = $user->subscription;

        // Check specific limit based on type
        switch ($limitType) {
            case 'deliveries':
                if ($subscription->deliveryLimitReached()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You have reached your delivery limit for this billing period',
                        'current_usage' => $subscription->getCurrentUsage('deliveries'),
                        'limit' => $subscription->max_deliveries,
                        'code' => 'LIMIT_REACHED',
                    ], 403);
                }
                break;

            case 'users':
                $currentUsers = \App\Models\User::where('tenant_id', $user->tenant_id)->count();
                if ($subscription->max_users !== null && $currentUsers >= $subscription->max_users) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You have reached your user limit',
                        'current_users' => $currentUsers,
                        'limit' => $subscription->max_users,
                        'code' => 'USER_LIMIT_REACHED',
                    ], 403);
                }
                break;

            case 'locations':
                // Check location limit if needed
                // For now, just pass through
                break;
        }

        return $next($request);
    }
}
