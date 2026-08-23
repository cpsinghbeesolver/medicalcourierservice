<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\DeliveryRequest;
use App\Models\Delivery;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MobileJobRequestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/mobile/v1/job-requests",
     *     summary="Get pending job requests",
     *     description="Returns all pending delivery requests for the current driver",
     *     tags={"Mobile - Job Requests"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pending job requests",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="requests", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="pagination", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Not a driver")
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'driver') {
            return response()->json([
                'success' => false,
                'message' => 'This endpoint is only for drivers'
            ], 403);
        }

        $perPage = min($request->input('per_page', 15), 50);

        $requests = DeliveryRequest::with(['delivery.items'])
            ->where('driver_id', $user->id)
            ->where('status', 'pending')
            ->orderBy('requested_at', 'desc')
            ->paginate($perPage);

        $transformedRequests = $requests->map(function($jobRequest) {
            $delivery = $jobRequest->delivery;
            return [
                'id' => $jobRequest->id,
                'status' => $jobRequest->status,
                'requested_at' => $jobRequest->requested_at->toIso8601String(),
                'delivery' => [
                    'id' => $delivery->id,
                    'delivery_number' => $delivery->delivery_number,
                    'priority' => $delivery->priority,
                    'urgency_level' => $delivery->urgency_level,
                    'pickup' => [
                        'name' => $delivery->pickup_name,
                        'address' => $delivery->pickup_address,
                        'city' => $delivery->pickup_city,
                        'phone' => $delivery->pickup_phone,
                        'scheduled_time' => $delivery->pickup_scheduled_time,
                        'location' => [
                            'latitude' => $delivery->pickup_latitude,
                            'longitude' => $delivery->pickup_longitude
                        ]
                    ],
                    'delivery' => [
                        'name' => $delivery->delivery_name,
                        'address' => $delivery->delivery_address,
                        'city' => $delivery->delivery_city,
                        'phone' => $delivery->delivery_phone,
                        'scheduled_time' => $delivery->delivery_scheduled_time,
                        'location' => [
                            'latitude' => $delivery->delivery_latitude,
                            'longitude' => $delivery->delivery_longitude
                        ]
                    ],
                    'items_count' => $delivery->items->count(),
                    'distance_km' => $delivery->distance_km,
                    'estimated_duration_minutes' => $delivery->estimated_duration_minutes,
                    'special_instructions' => $delivery->special_instructions,
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'requests' => $transformedRequests,
                'pagination' => [
                    'total' => $requests->total(),
                    'per_page' => $requests->perPage(),
                    'current_page' => $requests->currentPage(),
                    'last_page' => $requests->lastPage(),
                    'from' => $requests->firstItem(),
                    'to' => $requests->lastItem()
                ]
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/job-requests/{id}/accept",
     *     summary="Accept a job request",
     *     description="Accept a pending delivery request and assign delivery to driver",
     *     tags={"Mobile - Job Requests"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Job Request ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="latitude", type="number", format="float", example=40.7580),
     *             @OA\Property(property="longitude", type="number", format="float", example=-73.9855)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Job request accepted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Job request not found"),
     *     @OA\Response(response=400, description="Job request already processed")
     * )
     */
    public function accept(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role !== 'driver') {
            return response()->json([
                'success' => false,
                'message' => 'This endpoint is only for drivers'
            ], 403);
        }

        $jobRequest = DeliveryRequest::with('delivery')
            ->where('id', $id)
            ->where('driver_id', $user->id)
            ->first();

        if (!$jobRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Job request not found'
            ], 200);
        }

        if ($jobRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Job request has already been ' . $jobRequest->status
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Calculate response time
            $responseTime = now()->diffInSeconds($jobRequest->requested_at);

            // Update job request
            $jobRequest->status = 'accepted';
            $jobRequest->responded_at = now();
            $jobRequest->response_time_seconds = $responseTime;
            $jobRequest->save();

            // Update delivery status
            $delivery = $jobRequest->delivery;
            $delivery->status = 'assigned';
            $delivery->accepted_by_driver_at = now();
            $delivery->save();

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'accepted',
                'model_type' => 'App\Models\DeliveryRequest',
                'model_id' => $jobRequest->id,
                'description' => "Driver accepted delivery request {$delivery->delivery_number}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Job request accepted successfully',
                'data' => [
                    'request_id' => $jobRequest->id,
                    'delivery_id' => $delivery->id,
                    'delivery_number' => $delivery->delivery_number,
                    'status' => $jobRequest->status,
                    'response_time_seconds' => $responseTime,
                    'delivery_status' => $delivery->status
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to accept job request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/job-requests/{id}/decline",
     *     summary="Decline a job request",
     *     description="Decline a pending delivery request with reason",
     *     tags={"Mobile - Job Requests"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Job Request ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"decline_reason_category"},
     *             @OA\Property(
     *                 property="decline_reason_category",
     *                 type="string",
     *                 enum={"too_far", "time_conflict", "vehicle_issue", "personal", "other"},
     *                 example="time_conflict"
     *             ),
     *             @OA\Property(property="decline_reason", type="string", example="Already have another delivery scheduled at that time"),
     *             @OA\Property(property="latitude", type="number", format="float"),
     *             @OA\Property(property="longitude", type="number", format="float")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Job request declined successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function decline(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role !== 'driver') {
            return response()->json([
                'success' => false,
                'message' => 'This endpoint is only for drivers'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'decline_reason_category' => 'required|in:too_far,time_conflict,vehicle_issue,personal,other',
            'decline_reason' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $jobRequest = DeliveryRequest::with('delivery')
            ->where('id', $id)
            ->where('driver_id', $user->id)
            ->first();

        if (!$jobRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Job request not found'
            ], 404);
        }

        if ($jobRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Job request has already been ' . $jobRequest->status
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Calculate response time
            $responseTime = now()->diffInSeconds($jobRequest->requested_at);

            // Update job request
            $jobRequest->status = 'declined';
            $jobRequest->responded_at = now();
            $jobRequest->response_time_seconds = $responseTime;
            $jobRequest->decline_reason = $request->decline_reason;
            $jobRequest->decline_reason_category = $request->decline_reason_category;
            $jobRequest->save();

            // Update delivery status back to pending (can be assigned to another driver)
            $delivery = $jobRequest->delivery;
            $delivery->status = 'pending';
            $delivery->driver_id = null; // Remove driver assignment
            $delivery->save();

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'declined',
                'model_type' => 'App\Models\DeliveryRequest',
                'model_id' => $jobRequest->id,
                'description' => "Driver declined delivery request {$delivery->delivery_number}: {$request->decline_reason_category}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Job request declined successfully',
                'data' => [
                    'request_id' => $jobRequest->id,
                    'status' => $jobRequest->status,
                    'response_time_seconds' => $responseTime
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to decline job request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/job-requests/history",
     *     summary="Get job request history",
     *     description="Returns paginated history of all job requests (accepted and declined)",
     *     tags={"Mobile - Job Requests"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"accepted", "declined", "expired"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Job request history",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function history(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'driver') {
            return response()->json([
                'success' => false,
                'message' => 'This endpoint is only for drivers'
            ], 403);
        }

        $perPage = min($request->input('per_page', 20), 50);

        $query = DeliveryRequest::with(['delivery'])
            ->where('driver_id', $user->id)
            ->whereIn('status', ['accepted', 'declined', 'expired']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('responded_at', 'desc')
            ->paginate($perPage);

        $transformedRequests = $requests->map(function($jobRequest) {
            $delivery = $jobRequest->delivery;
            return [
                'id' => $jobRequest->id,
                'status' => $jobRequest->status,
                'requested_at' => $jobRequest->requested_at->toIso8601String(),
                'responded_at' => $jobRequest->responded_at?->toIso8601String(),
                'response_time_seconds' => $jobRequest->response_time_seconds,
                'decline_reason_category' => $jobRequest->decline_reason_category,
                'decline_reason' => $jobRequest->decline_reason,
                'delivery' => [
                    'id' => $delivery->id,
                    'delivery_number' => $delivery->delivery_number,
                    'status' => $delivery->status,
                    'priority' => $delivery->priority,
                    'pickup_name' => $delivery->pickup_name,
                    'pickup_city' => $delivery->pickup_city,
                    'delivery_name' => $delivery->delivery_name,
                    'delivery_city' => $delivery->delivery_city,
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'requests' => $transformedRequests,
                'pagination' => [
                    'total' => $requests->total(),
                    'per_page' => $requests->perPage(),
                    'current_page' => $requests->currentPage(),
                    'last_page' => $requests->lastPage(),
                    'from' => $requests->firstItem(),
                    'to' => $requests->lastItem()
                ]
            ]
        ]);
    }
}
