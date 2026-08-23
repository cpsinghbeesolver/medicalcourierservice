<?php

namespace App\Http\Controllers\Api\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/profile/details",
     *     summary="Get user profile with subscription details",
     *     tags={"Profile & Subscription"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com"),
     *                     @OA\Property(property="role", type="string", example="admin")
     *                 ),
     *                 @OA\Property(property="subscription", type="object",
     *                     @OA\Property(property="plan_name", type="string", example="professional"),
     *                     @OA\Property(property="plan_display_name", type="string", example="Professional (Multi-Site)"),
     *                     @OA\Property(property="plan_price", type="string", example="149.00"),
     *                     @OA\Property(property="status", type="string", example="active"),
     *                     @OA\Property(property="limits", type="object",
     *                         @OA\Property(property="max_deliveries", type="integer", example=2500),
     *                         @OA\Property(property="max_users", type="integer", example=25),
     *                         @OA\Property(property="max_locations", type="integer", example=10)
     *                     ),
     *                     @OA\Property(property="usage", type="object",
     *                         @OA\Property(property="deliveries", type="integer", example=127),
     *                         @OA\Property(property="users", type="integer", example=8)
     *                     ),
     *                     @OA\Property(property="features", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="key", type="string", example="live_gps"),
     *                             @OA\Property(property="name", type="string", example="Live GPS Tracking"),
     *                             @OA\Property(property="is_enabled", type="boolean", example=true)
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        // Load relationships
        $user->load([
            'subscription.features',
            'subscription.usage' => function ($query) {
                $query->where('period_date', now()->format('Y-m-d'));
            },
            'driverProfile'
        ]);

        $response = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'profile_photo' => $user->profile_photo,
                'status' => $user->status,
                'created_at' => $user->created_at,
            ],
        ];

        // Add subscription details if exists
        if ($user->subscription) {
            $subscription = $user->subscription;

            $response['subscription'] = [
                'id' => $subscription->id,
                'plan_name' => $subscription->plan_name,
                'plan_display_name' => $subscription->plan_display_name,
                'plan_price' => $subscription->plan_price,
                'billing_cycle' => $subscription->billing_cycle,
                'status' => $subscription->status,
                'is_active' => $subscription->isActive(),
                'on_trial' => $subscription->onTrial(),
                'trial_ends_at' => $subscription->trial_ends_at,
                'current_period_start' => $subscription->current_period_start,
                'current_period_end' => $subscription->current_period_end,
                'cancelled_at' => $subscription->cancelled_at,
                'limits' => [
                    'max_deliveries' => $subscription->max_deliveries,
                    'max_users' => $subscription->max_users,
                    'max_locations' => $subscription->max_locations,
                    'data_retention_days' => $subscription->data_retention_days,
                ],
                'usage' => [
                    'deliveries' => $subscription->getCurrentUsage('deliveries'),
                    'users' => $subscription->getCurrentUsage('users'),
                    'api_calls' => $subscription->getCurrentUsage('api_calls'),
                ],
                'features' => $subscription->features->map(function ($feature) {
                    return [
                        'key' => $feature->feature_key,
                        'name' => \App\Models\SubscriptionFeature::getDisplayName($feature->feature_key),
                        'is_enabled' => $feature->is_enabled,
                        'limit_value' => $feature->limit_value,
                    ];
                }),
            ];
        } else {
            $response['subscription'] = null;
        }

        // Add driver profile if exists
        if ($user->driverProfile) {
            $response['driver_profile'] = [
                'id' => $user->driverProfile->id,
                'license_number' => $user->driverProfile->license_number,
                'vehicle_type' => $user->driverProfile->vehicle_type,
                'vehicle_plate' => $user->driverProfile->vehicle_plate,
                'is_available' => $user->driverProfile->is_available,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $response,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/profile/subscription-usage",
     *     summary="Get subscription usage summary",
     *     tags={"Profile & Subscription"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Usage retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="period", type="object",
     *                     @OA\Property(property="start", type="string", format="date-time"),
     *                     @OA\Property(property="end", type="string", format="date-time")
     *                 ),
     *                 @OA\Property(property="metrics", type="object",
     *                     @OA\Property(property="deliveries", type="object",
     *                         @OA\Property(property="current", type="integer", example=127),
     *                         @OA\Property(property="limit", type="integer", example=2500),
     *                         @OA\Property(property="percentage", type="number", example=5.08)
     *                     ),
     *                     @OA\Property(property="users", type="object",
     *                         @OA\Property(property="current", type="integer", example=8),
     *                         @OA\Property(property="limit", type="integer", example=25),
     *                         @OA\Property(property="percentage", type="number", example=32.0)
     *                     )
     *                 ),
     *                 @OA\Property(property="warnings", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="No active subscription found")
     * )
     */
    public function subscriptionUsage(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->subscription) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active subscription found',
            ], 404);
        }

        $subscription = $user->subscription;

        $usage = [
            'period' => [
                'start' => $subscription->current_period_start,
                'end' => $subscription->current_period_end,
            ],
            'metrics' => [
                'deliveries' => [
                    'current' => $subscription->getCurrentUsage('deliveries'),
                    'limit' => $subscription->max_deliveries,
                    'percentage' => $subscription->max_deliveries
                        ? round(($subscription->getCurrentUsage('deliveries') / $subscription->max_deliveries) * 100, 2)
                        : 0,
                ],
                'users' => [
                    'current' => $subscription->getCurrentUsage('users'),
                    'limit' => $subscription->max_users,
                    'percentage' => $subscription->max_users
                        ? round(($subscription->getCurrentUsage('users') / $subscription->max_users) * 100, 2)
                        : 0,
                ],
                'api_calls' => [
                    'current' => $subscription->getCurrentUsage('api_calls'),
                    'limit' => null, // No hard limit
                ],
            ],
            'warnings' => [],
        ];

        // Add warnings if close to limits
        if ($subscription->max_deliveries && $subscription->getCurrentUsage('deliveries') >= $subscription->max_deliveries * 0.9) {
            $usage['warnings'][] = 'You are approaching your delivery limit for this period.';
        }

        if ($subscription->max_users && $subscription->getCurrentUsage('users') >= $subscription->max_users * 0.9) {
            $usage['warnings'][] = 'You are approaching your user limit.';
        }

        return response()->json([
            'status' => 'success',
            'data' => $usage,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/profile/check-feature/{featureKey}",
     *     summary="Check if user has access to a specific feature",
     *     tags={"Profile & Subscription"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="featureKey",
     *         in="path",
     *         required=true,
     *         description="Feature key to check (e.g., live_gps, custom_reports)",
     *         @OA\Schema(
     *             type="string",
     *             enum={"live_gps","custom_reports","photo_verification","multi_location","api_access","push_notifications","barcode_scanning","temperature_tracking","hipaa_audit_logs","white_label","sso","priority_support","bio_hazard_tracking","automated_compliance"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Feature access checked",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="has_access", type="boolean", example=true),
     *                 @OA\Property(property="feature", type="string", example="live_gps"),
     *                 @OA\Property(property="feature_name", type="string", example="Live GPS Tracking")
     *             )
     *         )
     *     )
     * )
     */
    public function checkFeature(Request $request, string $featureKey): JsonResponse
    {
        $user = $request->user();

        if (!$user->subscription) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'has_access' => false,
                    'feature' => $featureKey,
                    'reason' => 'No active subscription',
                ],
            ]);
        }

        $hasFeature = $user->subscription->hasFeature($featureKey);

        return response()->json([
            'status' => 'success',
            'data' => [
                'has_access' => $hasFeature,
                'feature' => $featureKey,
                'feature_name' => \App\Models\SubscriptionFeature::getDisplayName($featureKey),
            ],
        ]);
    }

    public function searchNames(Request $request): JsonResponse
    {
        $query = $request->query('query');

        if (!$query) {
            return response()->json([
                'status' => 'error',
                'message' => 'Query parameter is required',
            ], 400);
        }

        //add search driver profile modal created by current user id and search by delivery number created by current user id
        $deliveries = \App\Models\Delivery::where('created_by', auth()->id())
            ->where('delivery_number', 'like', "%{$query}%")
            ->get()
            ->map(function ($delivery) {
                return collect([
                    'id'    => $delivery->id,
                    'title' => $delivery->delivery_number,
                    'type'  => 'Job',
                ]); 
            });

        $drivers = \App\Models\DriverProfile::with('user')
        ->where('created_by', auth()->id())
        ->get()
        ->filter(function ($driver) use ($query) {
            $query = strtolower(trim($query));

            $name = strtolower($driver->user->name ?? '');
            $email = strtolower($driver->user->email ?? '');

            return str_contains($name, $query)
                || str_contains($email, $query);
        })
        ->map(function ($driver) {
            return [
                'id' => $driver->id,
                'title' => $driver->user->name,
                'type' => 'Driver',
            ];
        })
        ->values();
        
        if($deliveries->isEmpty() && $drivers->isNotEmpty()) {
            $users = $drivers;
        }else{
            $users = $deliveries->merge($drivers)->values();
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $users,
        ]);
    }
}
