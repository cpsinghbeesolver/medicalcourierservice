<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DriverProfile;
use App\Models\ActivityLog;
use App\Http\Resources\DeliveryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    /**
     * @OA\Get(
     *     path="/driver/deliveries",
     *     summary="Get driver's assigned deliveries",
     *     tags={"Driver Operations"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"assigned","in_transit","picked_up","delivered"})
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Filter by date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Driver deliveries retrieved successfully"
     *     )
     * )
     */
    public function myDeliveries(Request $request)
    {
        $user = $request->user();

        if (!$user->isDriver()) {
            return $this->errorResponse('Only drivers can access this endpoint', 403);
        }

        $query = Delivery::where('driver_id', $user->id)
            ->with(['items', 'verifications']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            // By default, exclude cancelled and delivered
            $query->whereNotIn('status', ['cancelled', 'delivered']);
        }

        // Filter by date
        if ($request->has('date')) {
            $query->whereDate('pickup_scheduled_time', $request->date);
        }

        // Sort by pickup scheduled time
        $query->orderBy('pickup_scheduled_time', 'asc');

        $deliveries = $query->get();

        return $this->successResponse([
            'deliveries' => DeliveryResource::collection($deliveries),
            'summary' => [
                'total' => $deliveries->count(),
                'assigned' => $deliveries->where('status', 'assigned')->count(),
                'in_transit' => $deliveries->where('status', 'in_transit')->count(),
                'picked_up' => $deliveries->where('status', 'picked_up')->count(),
            ]
        ], 'Driver deliveries retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/driver/deliveries/available",
     *     summary="Get available unassigned deliveries",
     *     tags={"Driver Operations"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="max_distance",
     *         in="query",
     *         description="Maximum distance in km from driver's location",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="priority",
     *         in="query",
     *         description="Filter by priority",
     *         required=false,
     *         @OA\Schema(type="string", enum={"low","normal","high","urgent"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Available deliveries retrieved successfully"
     *     )
     * )
     */
    public function availableDeliveries(Request $request)
    {
        $user = $request->user();

        if (!$user->isDriver()) {
            return $this->errorResponse('Only drivers can access this endpoint', 403);
        }

        $query = Delivery::where('status', 'pending')
            ->with(['items'])
            ->whereNull('driver_id');

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by date range - upcoming deliveries
        $query->where('pickup_scheduled_time', '>=', now());

        // Sort by priority and scheduled time
        $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')")
            ->orderBy('pickup_scheduled_time', 'asc');

        $deliveries = $query->get();

        // If driver has location and max_distance is provided, filter by distance
        if ($request->has('max_distance') && $user->driverProfile) {
            $driverLat = $user->driverProfile->current_latitude;
            $driverLng = $user->driverProfile->current_longitude;

            if ($driverLat && $driverLng) {
                $maxDistance = $request->max_distance;
                $deliveries = $deliveries->filter(function ($delivery) use ($driverLat, $driverLng, $maxDistance) {
                    if ($delivery->pickup_latitude && $delivery->pickup_longitude) {
                        $distance = $this->calculateDistance(
                            $driverLat,
                            $driverLng,
                            $delivery->pickup_latitude,
                            $delivery->pickup_longitude
                        );
                        return $distance <= $maxDistance;
                    }
                    return true;
                });
            }
        }

        return $this->successResponse([
            'deliveries' => DeliveryResource::collection($deliveries),
            'total' => $deliveries->count(),
        ], 'Available deliveries retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/driver/location",
     *     summary="Update driver's current location",
     *     tags={"Driver Operations"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"latitude","longitude"},
     *             @OA\Property(property="latitude", type="number", format="float", example=40.7128),
     *             @OA\Property(property="longitude", type="number", format="float", example=-74.0060)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Location updated successfully"
     *     ),
     *     @OA\Response(response=403, description="Not a driver"),
     *     @OA\Response(response=404, description="Driver profile not found")
     * )
     */
    public function updateLocation(Request $request)
    {
        $user = $request->user();

        if (!$user->isDriver()) {
            return $this->errorResponse('Only drivers can update location', 403);
        }

        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $driverProfile = $user->driverProfile;

        if (!$driverProfile) {
            return $this->errorResponse('Driver profile not found', 404);
        }

        $driverProfile->updateLocation(
            $request->latitude,
            $request->longitude
        );

        // Log activity (optional, could be too frequent)
        // Uncomment if you want to track location updates
        /*
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'location_updated',
            'model_type' => DriverProfile::class,
            'model_id' => $driverProfile->id,
            'description' => 'Driver location updated',
            'properties' => [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        */

        return $this->successResponse([
            'latitude' => $driverProfile->current_latitude,
            'longitude' => $driverProfile->current_longitude,
            'updated_at' => $driverProfile->updated_at->toIso8601String(),
        ], 'Location updated successfully');
    }

    /**
     * @OA\Post(
     *     path="/driver/availability",
     *     summary="Update driver's availability status",
     *     tags={"Driver Operations"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"available","busy","off_duty"}, example="available")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Availability updated successfully"
     *     )
     * )
     */
    public function updateAvailability(Request $request)
    {
        $user = $request->user();

        if (!$user->isDriver()) {
            return $this->errorResponse('Only drivers can update availability', 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:available,busy,off_duty',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $driverProfile = $user->driverProfile;

        if (!$driverProfile) {
            return $this->errorResponse('Driver profile not found', 404);
        }

        $driverProfile->update([
            'availability_status' => $request->status,
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'availability_updated',
            'model_type' => DriverProfile::class,
            'model_id' => $driverProfile->id,
            'description' => "Driver availability changed to {$request->status}",
            'properties' => ['status' => $request->status],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $this->successResponse([
            'availability_status' => $driverProfile->availability_status,
            'updated_at' => $driverProfile->updated_at->toIso8601String(),
        ], 'Availability updated successfully');
    }

    /**
     * @OA\Get(
     *     path="/driver/statistics",
     *     summary="Get driver performance statistics",
     *     tags={"Driver Operations"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="period",
     *         in="query",
     *         description="Time period for statistics",
     *         required=false,
     *         @OA\Schema(type="string", enum={"today","week","month","all"}, default="month")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Statistics retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Statistics retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="total_deliveries", type="integer", example=45),
     *                 @OA\Property(property="completed_deliveries", type="integer", example=42),
     *                 @OA\Property(property="pending_deliveries", type="integer", example=2),
     *                 @OA\Property(property="cancelled_deliveries", type="integer", example=1),
     *                 @OA\Property(property="completion_rate", type="number", format="float", example=93.33),
     *                 @OA\Property(property="on_time_deliveries", type="integer", example=38),
     *                 @OA\Property(property="on_time_rate", type="number", format="float", example=90.48),
     *                 @OA\Property(property="total_distance_km", type="number", format="float", example=450.5),
     *                 @OA\Property(property="average_delivery_time_minutes", type="number", format="float", example=35.2)
     *             )
     *         )
     *     )
     * )
     */
    public function statistics(Request $request)
    {
        $user = $request->user();

        if (!$user->isDriver()) {
            return $this->errorResponse('Only drivers can access statistics', 403);
        }

        $period = $request->get('period', 'month');
        $query = Delivery::where('driver_id', $user->id);

        // Apply period filter
        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
                break;
            case 'all':
            default:
                // No date filter
                break;
        }

        $deliveries = $query->get();

        $totalDeliveries = $deliveries->count();
        $completedDeliveries = $deliveries->where('status', 'delivered')->count();
        $pendingDeliveries = $deliveries->whereIn('status', ['assigned', 'in_transit', 'picked_up'])->count();
        $cancelledDeliveries = $deliveries->where('status', 'cancelled')->count();

        // Calculate completion rate
        $completionRate = $totalDeliveries > 0
            ? round(($completedDeliveries / $totalDeliveries) * 100, 2)
            : 0;

        // Calculate on-time deliveries
        $onTimeDeliveries = $deliveries->filter(function ($delivery) {
            return $delivery->status === 'delivered'
                && $delivery->delivery_actual_time
                && $delivery->delivery_scheduled_time
                && $delivery->delivery_actual_time <= $delivery->delivery_scheduled_time;
        })->count();

        $onTimeRate = $completedDeliveries > 0
            ? round(($onTimeDeliveries / $completedDeliveries) * 100, 2)
            : 0;

        // Calculate total distance
        $totalDistance = $deliveries->sum('distance_km') ?? 0;

        // Calculate average delivery time (from pickup to delivery)
        $completedWithTimes = $deliveries->filter(function ($delivery) {
            return $delivery->status === 'delivered'
                && $delivery->pickup_actual_time
                && $delivery->delivery_actual_time;
        });

        $averageDeliveryTime = 0;
        if ($completedWithTimes->count() > 0) {
            $totalMinutes = $completedWithTimes->sum(function ($delivery) {
                return $delivery->pickup_actual_time->diffInMinutes($delivery->delivery_actual_time);
            });
            $averageDeliveryTime = round($totalMinutes / $completedWithTimes->count(), 2);
        }

        // Status breakdown
        $statusBreakdown = [
            'assigned' => $deliveries->where('status', 'assigned')->count(),
            'in_transit' => $deliveries->where('status', 'in_transit')->count(),
            'picked_up' => $deliveries->where('status', 'picked_up')->count(),
            'delivered' => $completedDeliveries,
            'cancelled' => $cancelledDeliveries,
        ];

        // Priority breakdown
        $priorityBreakdown = [
            'low' => $deliveries->where('priority', 'low')->count(),
            'normal' => $deliveries->where('priority', 'normal')->count(),
            'high' => $deliveries->where('priority', 'high')->count(),
            'urgent' => $deliveries->where('priority', 'urgent')->count(),
        ];

        // Recent deliveries (last 5 completed)
        $recentDeliveries = Delivery::where('driver_id', $user->id)
            ->where('status', 'delivered')
            ->orderBy('delivery_actual_time', 'desc')
            ->limit(5)
            ->get();

        return $this->successResponse([
            'period' => $period,
            'summary' => [
                'total_deliveries' => $totalDeliveries,
                'completed_deliveries' => $completedDeliveries,
                'pending_deliveries' => $pendingDeliveries,
                'cancelled_deliveries' => $cancelledDeliveries,
                'completion_rate' => $completionRate,
                'on_time_deliveries' => $onTimeDeliveries,
                'on_time_rate' => $onTimeRate,
                'total_distance_km' => round($totalDistance, 2),
                'average_delivery_time_minutes' => $averageDeliveryTime,
            ],
            'breakdown' => [
                'by_status' => $statusBreakdown,
                'by_priority' => $priorityBreakdown,
            ],
            'recent_deliveries' => DeliveryResource::collection($recentDeliveries),
        ], 'Statistics retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/driver/clock-in",
     *     summary="Clock in for shift",
     *     tags={"Driver Operations"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="latitude", type="number", format="float", example=40.7128),
     *             @OA\Property(property="longitude", type="number", format="float", example=-74.0060)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Clocked in successfully"
     *     ),
     *     @OA\Response(response=403, description="Not a driver"),
     *     @OA\Response(response=400, description="Already clocked in")
     * )
     */
    public function clockIn(Request $request)
    {
        $user = $request->user();

        if (!$user->isDriver()) {
            return $this->errorResponse('Only drivers can clock in', 403);
        }

        $driverProfile = $user->driverProfile;

        if (!$driverProfile) {
            return $this->errorResponse('Driver profile not found', 404);
        }

        if ($driverProfile->is_clocked_in) {
            return $this->errorResponse('You are already clocked in', 400);
        }

        $validator = Validator::make($request->all(), [
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        // Clock in the driver
        $driverProfile->clockIn();

        // Update location if provided
        if ($request->has('latitude') && $request->has('longitude')) {
            $driverProfile->updateLocation(
                $request->latitude,
                $request->longitude
            );
        }

        // Log activity
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'driver_clocked_in',
            'model_type' => DriverProfile::class,
            'model_id' => $driverProfile->id,
            'description' => 'Driver clocked in for shift',
            'properties' => [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'clocked_in_at' => $driverProfile->clocked_in_at,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $this->successResponse([
            'is_clocked_in' => $driverProfile->is_clocked_in,
            'clocked_in_at' => $driverProfile->clocked_in_at->toIso8601String(),
            'availability_status' => $driverProfile->availability_status,
            'current_latitude' => $driverProfile->current_latitude,
            'current_longitude' => $driverProfile->current_longitude,
        ], 'Clocked in successfully');
    }

    /**
     * @OA\Post(
     *     path="/driver/clock-out",
     *     summary="Clock out from shift",
     *     tags={"Driver Operations"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Clocked out successfully"
     *     ),
     *     @OA\Response(response=403, description="Not a driver"),
     *     @OA\Response(response=400, description="Not clocked in")
     * )
     */
    public function clockOut(Request $request)
    {
        $user = $request->user();

        if (!$user->isDriver()) {
            return $this->errorResponse('Only drivers can clock out', 403);
        }

        $driverProfile = $user->driverProfile;

        if (!$driverProfile) {
            return $this->errorResponse('Driver profile not found', 404);
        }

        if (!$driverProfile->is_clocked_in) {
            return $this->errorResponse('You are not currently clocked in', 400);
        }

        // Check if driver has any active deliveries
        $activeDeliveries = Delivery::where('driver_id', $user->id)
            ->whereIn('status', ['assigned', 'in_transit', 'picked_up'])
            ->count();

        if ($activeDeliveries > 0) {
            return $this->errorResponse(
                "Cannot clock out with {$activeDeliveries} active deliveries. Please complete or reassign them first.",
                400
            );
        }

        $clockedInDuration = $driverProfile->clocked_in_at->diffInMinutes(now());

        // Clock out the driver
        $driverProfile->clockOut();

        // Log activity
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'driver_clocked_out',
            'model_type' => DriverProfile::class,
            'model_id' => $driverProfile->id,
            'description' => 'Driver clocked out from shift',
            'properties' => [
                'duration_minutes' => $clockedInDuration,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $this->successResponse([
            'is_clocked_in' => $driverProfile->is_clocked_in,
            'availability_status' => $driverProfile->availability_status,
            'shift_duration_minutes' => $clockedInDuration,
        ], 'Clocked out successfully');
    }

    /**
     * @OA\Get(
     *     path="/driver/clock-status",
     *     summary="Get current clock-in status",
     *     tags={"Driver Operations"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Clock status retrieved successfully"
     *     )
     * )
     */
    public function clockStatus(Request $request)
    {
        $user = $request->user();

        if (!$user->isDriver()) {
            return $this->errorResponse('Only drivers can access clock status', 403);
        }

        $driverProfile = $user->driverProfile;

        if (!$driverProfile) {
            return $this->errorResponse('Driver profile not found', 404);
        }

        $response = [
            'is_clocked_in' => $driverProfile->is_clocked_in,
            'availability_status' => $driverProfile->availability_status,
            'clocked_in_at' => $driverProfile->clocked_in_at ? $driverProfile->clocked_in_at->toIso8601String() : null,
            'shift_duration_minutes' => null,
        ];

        if ($driverProfile->is_clocked_in && $driverProfile->clocked_in_at) {
            $response['shift_duration_minutes'] = $driverProfile->clocked_in_at->diffInMinutes(now());
        }

        return $this->successResponse($response, 'Clock status retrieved successfully');
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in kilometers
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return round($distance, 2);
    }
}
