<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\DriverProfile;
use App\Models\Delivery;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MobileDriverController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/mobile/v1/driver/location",
     *     summary="Update driver GPS location",
     *     description="Updates the driver's current GPS location for real-time tracking",
     *     tags={"Mobile - Driver"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"latitude", "longitude"},
     *             @OA\Property(property="latitude", type="number", format="float", example=40.7580, description="Latitude (-90 to 90)"),
     *             @OA\Property(property="longitude", type="number", format="float", example=-73.9855, description="Longitude (-180 to 180)"),
     *             @OA\Property(property="speed", type="number", format="float", example=45.5, description="Speed in km/h"),
     *             @OA\Property(property="heading", type="number", format="float", example=180.0, description="Heading in degrees (0-360)"),
     *             @OA\Property(property="accuracy", type="number", format="float", example=10.0, description="Accuracy in meters")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Location updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Location updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="latitude", type="number"),
     *                 @OA\Property(property="longitude", type="number"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Not a driver"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateLocation(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'driver') {
            return response()->json([
                'success' => false,
                'message' => 'This endpoint is only for drivers'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed' => 'nullable|numeric|min:0',
            'heading' => 'nullable|numeric|between:0,360',
            'accuracy' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $driverProfile = DriverProfile::where('user_id', $user->id)->first();

        if (!$driverProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Driver profile not found'
            ], 404);
        }

        $driverProfile->current_latitude = $request->latitude;
        $driverProfile->current_longitude = $request->longitude;
        $driverProfile->save();

        // Log location update
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'location_updated',
            'model_type' => 'App\Models\DriverProfile',
            'model_id' => $driverProfile->id,
            'description' => "Driver location updated to ({$request->latitude}, {$request->longitude})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully',
            'data' => [
                'latitude' => $driverProfile->current_latitude,
                'longitude' => $driverProfile->current_longitude,
                'updated_at' => $driverProfile->updated_at->toIso8601String()
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/driver/availability",
     *     summary="Update driver availability status",
     *     description="Updates the driver's availability status for assignment",
     *     tags={"Mobile - Driver"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"availability_status"},
     *             @OA\Property(
     *                 property="availability_status",
     *                 type="string",
     *                 enum={"available", "busy", "off_duty"},
     *                 example="available",
     *                 description="Driver's current availability status"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Availability updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="availability_status", type="string"),
     *                 @OA\Property(property="updated_at", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Not a driver"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateAvailability(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'driver') {
            return response()->json([
                'success' => false,
                'message' => 'This endpoint is only for drivers'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'availability_status' => 'required|in:available,busy,off_duty'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $driverProfile = DriverProfile::where('user_id', $user->id)->first();

        if (!$driverProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Driver profile not found'
            ], 404);
        }

        $oldStatus = $driverProfile->availability_status;
        $driverProfile->availability_status = $request->availability_status;
        $driverProfile->save();

        // Log availability change
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'availability_changed',
            'model_type' => 'App\Models\DriverProfile',
            'model_id' => $driverProfile->id,
            'description' => "Driver availability changed from {$oldStatus} to {$request->availability_status}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Availability updated successfully',
            'data' => [
                'availability_status' => $driverProfile->availability_status,
                'updated_at' => $driverProfile->updated_at->toIso8601String()
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/driver/statistics",
     *     summary="Get driver statistics",
     *     description="Returns comprehensive statistics for the driver with optional pagination for recent deliveries",
     *     tags={"Mobile - Driver"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="period",
     *         in="query",
     *         description="Time period for statistics",
     *         required=false,
     *         @OA\Schema(type="string", enum={"today", "week", "month", "year", "all"}, default="month")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for recent deliveries",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page for recent deliveries",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Driver statistics",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="summary", type="object",
     *                     @OA\Property(property="total_deliveries", type="integer"),
     *                     @OA\Property(property="completed_deliveries", type="integer"),
     *                     @OA\Property(property="cancelled_deliveries", type="integer"),
     *                     @OA\Property(property="failed_deliveries", type="integer"),
     *                     @OA\Property(property="total_distance_km", type="number"),
     *                     @OA\Property(property="total_duration_hours", type="number"),
     *                     @OA\Property(property="completion_rate", type="number")
     *                 ),
     *                 @OA\Property(property="recent_deliveries", type="object",
     *                     @OA\Property(property="deliveries", type="array", @OA\Items(type="object")),
     *                     @OA\Property(property="pagination", type="object")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Not a driver")
     * )
     */
    public function statistics(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'driver') {
            return response()->json([
                'success' => false,
                'message' => 'This endpoint is only for drivers'
            ], 403);
        }

        $period = $request->input('period', 'month');
        $perPage = min($request->input('per_page', 10), 50);

        // Determine date range
        $dateFrom = null;
        switch ($period) {
            case 'today':
                $dateFrom = now()->startOfDay();
                break;
            case 'week':
                $dateFrom = now()->startOfWeek();
                break;
            case 'month':
                $dateFrom = now()->startOfMonth();
                break;
            case 'year':
                $dateFrom = now()->startOfYear();
                break;
            case 'all':
            default:
                $dateFrom = null;
                break;
        }

        // Build base query
        $query = Delivery::where('driver_id', $user->id);
        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }

        // Get summary statistics
        $totalDeliveries = $query->count();
        $completedDeliveries = (clone $query)->where('status', 'delivered')->count();
        $cancelledDeliveries = (clone $query)->where('status', 'cancelled')->count();
        $failedDeliveries = (clone $query)->where('status', 'failed')->count();
        $totalDistance = (clone $query)->sum('distance_km') ?? 0;
        $avgDuration = (clone $query)->whereNotNull('pickup_actual_time')
                                     ->whereNotNull('delivery_actual_time')
                                     ->avg(DB::raw('TIMESTAMPDIFF(MINUTE, pickup_actual_time, delivery_actual_time)')) ?? 0;

        $completionRate = $totalDeliveries > 0
            ? round(($completedDeliveries / $totalDeliveries) * 100, 2)
            : 0;

        // Get recent deliveries with pagination
        $recentQuery = Delivery::where('driver_id', $user->id)
                               ->orderBy('created_at', 'desc');

        if ($dateFrom) {
            $recentQuery->where('created_at', '>=', $dateFrom);
        }

        $recentDeliveries = $recentQuery->paginate($perPage);

        $transformedRecent = $recentDeliveries->map(function($delivery) {
            return [
                'id' => $delivery->id,
                'delivery_number' => $delivery->delivery_number,
                'status' => $delivery->status,
                'priority' => $delivery->priority,
                'pickup_name' => $delivery->pickup_name,
                'pickup_city' => $delivery->pickup_city,
                'delivery_name' => $delivery->delivery_name,
                'delivery_city' => $delivery->delivery_city,
                'distance_km' => $delivery->distance_km,
                'created_at' => $delivery->created_at->toIso8601String(),
                'completed_at' => $delivery->delivery_actual_time ?
                    $delivery->delivery_actual_time->toIso8601String() : null
            ];
        });

        // Get status breakdown
        $statusBreakdown = DB::table('deliveries')
            ->select('status', DB::raw('count(*) as count'))
            ->where('driver_id', $user->id);

        if ($dateFrom) {
            $statusBreakdown->where('created_at', '>=', $dateFrom);
        }

        $statusBreakdown = $statusBreakdown->groupBy('status')->get();

        // Get daily performance (last 7 days for week/month, last 30 days for year/all)
        $daysBack = in_array($period, ['year', 'all']) ? 30 : 7;
        $dailyPerformance = DB::table('deliveries')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total'),
                DB::raw('sum(case when status = "delivered" then 1 else 0 end) as completed'),
                DB::raw('sum(distance_km) as distance')
            )
            ->where('driver_id', $user->id)
            ->where('created_at', '>=', now()->subDays($daysBack))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $period,
                'summary' => [
                    'total_deliveries' => $totalDeliveries,
                    'completed_deliveries' => $completedDeliveries,
                    'cancelled_deliveries' => $cancelledDeliveries,
                    'failed_deliveries' => $failedDeliveries,
                    'total_distance_km' => round($totalDistance, 2),
                    'avg_duration_minutes' => round($avgDuration, 2),
                    'total_duration_hours' => round($avgDuration * $completedDeliveries / 60, 2),
                    'completion_rate' => $completionRate
                ],
                'status_breakdown' => $statusBreakdown->map(function($item) {
                    return [
                        'status' => $item->status,
                        'count' => $item->count
                    ];
                }),
                'daily_performance' => $dailyPerformance->map(function($item) {
                    return [
                        'date' => $item->date,
                        'total' => $item->total,
                        'completed' => $item->completed,
                        'distance_km' => round($item->distance ?? 0, 2)
                    ];
                }),
                'recent_deliveries' => [
                    'deliveries' => $transformedRecent,
                    'pagination' => [
                        'total' => $recentDeliveries->total(),
                        'per_page' => $recentDeliveries->perPage(),
                        'current_page' => $recentDeliveries->currentPage(),
                        'last_page' => $recentDeliveries->lastPage(),
                        'from' => $recentDeliveries->firstItem(),
                        'to' => $recentDeliveries->lastItem()
                    ]
                ]
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/driver/profile",
     *     summary="Get driver profile details",
     *     description="Returns complete driver profile information including vehicle and license details",
     *     tags={"Mobile - Driver"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Driver profile",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="license_number", type="string"),
     *                 @OA\Property(property="license_expiry_date", type="string", format="date"),
     *                 @OA\Property(property="vehicle_type", type="string"),
     *                 @OA\Property(property="vehicle_plate_number", type="string"),
     *                 @OA\Property(property="availability_status", type="string"),
     *                 @OA\Property(property="current_location", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Not a driver"),
     *     @OA\Response(response=404, description="Profile not found")
     * )
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'driver') {
            return response()->json([
                'success' => false,
                'message' => 'This endpoint is only for drivers'
            ], 403);
        }

        $driverProfile = DriverProfile::where('user_id', $user->id)->first();

        if (!$driverProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Driver profile not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $driverProfile->id,
                'user_id' => $driverProfile->user_id,
                'license_number' => $driverProfile->license_number,
                'license_expiry_date' => $driverProfile->license_expiry_date,
                'vehicle_type' => $driverProfile->vehicle_type,
                'vehicle_plate_number' => $driverProfile->vehicle_plate_number,
                'date_of_birth' => $driverProfile->date_of_birth?->format('Y-m-d'),
                'address' => $driverProfile->address,
                'city' => $driverProfile->city,
                'state' => $driverProfile->state,
                'zip_code' => $driverProfile->zip_code,
                'emergency_contact_name' => $driverProfile->emergency_contact_name,
                'emergency_contact_phone' => $driverProfile->emergency_contact_phone,
                'availability_status' => $driverProfile->availability_status,
                'current_location' => [
                    'latitude' => $driverProfile->current_latitude,
                    'longitude' => $driverProfile->current_longitude
                ],
                'created_at' => $driverProfile->created_at->toIso8601String(),
                'updated_at' => $driverProfile->updated_at->toIso8601String()
            ]
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/mobile/v1/driver/profile",
     *     summary="Update driver profile",
     *     description="Updates driver profile information",
     *     tags={"Mobile - Driver"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="vehicle_type", type="string"),
     *             @OA\Property(property="vehicle_plate_number", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="city", type="string"),
     *             @OA\Property(property="state", type="string"),
     *             @OA\Property(property="zip_code", type="string"),
     *             @OA\Property(property="emergency_contact_name", type="string"),
     *             @OA\Property(property="emergency_contact_phone", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'driver') {
            return response()->json([
                'success' => false,
                'message' => 'This endpoint is only for drivers'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'vehicle_type' => 'sometimes|string|max:255',
            'vehicle_plate_number' => 'sometimes|string|max:50',
            'address' => 'sometimes|string',
            'city' => 'sometimes|string|max:100',
            'state' => 'sometimes|string|max:50',
            'zip_code' => 'sometimes|string|max:20',
            'emergency_contact_name' => 'sometimes|string|max:255',
            'emergency_contact_phone' => 'sometimes|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $driverProfile = DriverProfile::where('user_id', $user->id)->first();

        if (!$driverProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Driver profile not found'
            ], 404);
        }

        $driverProfile->update($request->only([
            'vehicle_type',
            'vehicle_plate_number',
            'address',
            'city',
            'state',
            'zip_code',
            'emergency_contact_name',
            'emergency_contact_phone'
        ]));

        // Log profile update
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'updated',
            'model_type' => 'App\Models\DriverProfile',
            'model_id' => $driverProfile->id,
            'description' => "Driver profile updated",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $driverProfile
        ]);
    }

    public function availabilityStatus(Request $request){
        $user = $request->user()->load('driverProfile');
        if ($user->role !== 'driver') {
            return response()->json([
                'success' => false,
                'message' => 'This endpoint is only for drivers'
            ], 403);
        }
        return response()->json([
            'success' => true,
            'message' => 'Driver Availability status',
            'data' => $user->driverProfile->availability_status
        ]);
    }
}
