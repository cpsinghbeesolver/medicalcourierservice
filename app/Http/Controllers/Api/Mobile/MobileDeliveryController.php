<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\DeliveryVerification;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendFirebaseNotificationJob;
use App\Events\DeliveryStatusUpdated;

class MobileDeliveryController extends Controller
{
    public function __construct()
    {
        set_time_limit(120);  
    }
    /**
     * @OA\Get(
     *     path="/api/mobile/v1/deliveries",
     *     summary="Get paginated list of deliveries",
     *     description="Returns a paginated list of deliveries with advanced filtering, sorting, and search capabilities",
     *     tags={"Mobile - Deliveries"},
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
     *         description="Items per page (max 100)",
     *         required=false,
     *         @OA\Schema(type="integer", example=15, maximum=100)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"pending", "assigned", "in_transit", "picked_up", "delivered", "failed", "cancelled"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="priority",
     *         in="query",
     *         description="Filter by priority",
     *         required=false,
     *         @OA\Schema(type="string", enum={"low", "normal", "high", "urgent"})
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by delivery number, pickup or delivery location",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort by field",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"created_at", "pickup_scheduled_time", "priority", "delivery_number"},
     *             default="created_at"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sort order",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated deliveries list",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="deliveries", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="delivery_number", type="string"),
     *                     @OA\Property(property="status", type="string"),
     *                     @OA\Property(property="priority", type="string")
     *                 )),
     *                 @OA\Property(property="pagination", type="object",
     *                     @OA\Property(property="total", type="integer", example=145),
     *                     @OA\Property(property="per_page", type="integer", example=15),
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="last_page", type="integer", example=10),
     *                     @OA\Property(property="from", type="integer", example=1),
     *                     @OA\Property(property="to", type="integer", example=15)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request)
    {
        $perPage = min($request->input('per_page', 15), 100);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Filter by user role
        $user = $request->user();
        $driverProfile = $user->driverProfile;
        $query = Delivery::query()->where('status','assigned')->where('created_by', $driverProfile->created_by);
        
        if ($user->role === 'driver') {
            $query->where('driver_id', $user->id);
        } elseif ($user->role === 'dispatcher') {
            // Dispatchers can see all deliveries
        }

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('delivery_number', 'like', "%{$search}%")
                  ->orWhere('pickup_name', 'like', "%{$search}%")
                  ->orWhere('delivery_name', 'like', "%{$search}%")
                  ->orWhere('pickup_address', 'like', "%{$search}%")
                  ->orWhere('delivery_address', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $allowedSortFields = ['created_at', 'pickup_scheduled_time', 'priority', 'delivery_number'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Paginate with relationships
        $deliveries = $query->with(['driver:id,name,phone', 'creator:id,name', 'items'])
                            ->paginate($perPage);

        // Transform for mobile optimization
        $transformedDeliveries = $deliveries->map(function($delivery) {
            return [
                'id' => $delivery->id,
                'delivery_number' => $delivery->delivery_number,
                'status' => $delivery->status,
                'priority' => $delivery->priority,
                'pickup' => [
                    'name' => $delivery->pickup_name,
                    'address' => $delivery->pickup_address,
                    'city' => $delivery->pickup_city,
                    'phone' => $delivery->pickup_phone,
                    'scheduled_time' => $delivery->pickup_scheduled_time,
                    'actual_time' => $delivery->pickup_actual_time,
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
                    'actual_time' => $delivery->delivery_actual_time,
                    'requires_pickup_barcode_scan' => $delivery->requires_pickup_barcode_scan,
                    'requires_pickup_signature' => $delivery->requires_pickup_signature,
                    'requires_pickup_photo' => $delivery->requires_pickup_photo,
                    'requires_recepient_id_scan' => $delivery->requires_recepient_id_scan,
                    'requires_dropoff_signature' => $delivery->requires_dropoff_signature,
                    'requires_dropoff_barcode_scan' => $delivery->requires_dropoff_barcode_scan,
                    'requires_dropoff_photo' => $delivery->requires_dropoff_photo,
                    'location' => [
                        'latitude' => $delivery->delivery_latitude,
                        'longitude' => $delivery->delivery_longitude
                    ]
                ],
                'driver' => $delivery->driver ? [
                    'id' => $delivery->driver->id,
                    'name' => $delivery->driver->name,
                    'phone' => $delivery->driver->phone
                ] : null,
                'item_count' => $delivery->items->count(),
                'distance_km' => $delivery->distance_km,
                'estimated_duration_minutes' => $delivery->estimated_duration_minutes,
                'special_instructions' => $delivery->special_instructions,
                'created_at' => $delivery->created_at->toIso8601String()
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'deliveries' => $transformedDeliveries,
                'pagination' => [
                    'total' => $deliveries->total(),
                    'per_page' => $deliveries->perPage(),
                    'current_page' => $deliveries->currentPage(),
                    'last_page' => $deliveries->lastPage(),
                    'from' => $deliveries->firstItem(),
                    'to' => $deliveries->lastItem()
                ]
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/deliveries/{id}",
     *     summary="Get delivery details",
     *     description="Returns complete delivery information including items with barcode, dropoff locations, temperature requirements, vehicle requirements, and container count",
     *     tags={"Mobile - Deliveries"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Delivery ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Delivery details with all required fields",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=6),
     *                 @OA\Property(property="delivery_number", type="string", example="DEL-2026-0006"),
     *                 @OA\Property(property="status", type="string", example="assigned"),
     *                 @OA\Property(property="priority", type="string", example="high"),
     *                 @OA\Property(property="temperature_requirement", type="string", example="refrigerated", description="Temperature requirement for entire delivery"),
     *                 @OA\Property(property="vehicle_requirements", type="string", example="refrigerated_van", description="Required vehicle type"),
     *                 @OA\Property(property="container_count", type="integer", example=3, description="Number of containers or bags"),
     *                 @OA\Property(property="items", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="item_type", type="string", example="specimen"),
     *                         @OA\Property(property="specimen_type", type="string", example="blood"),
     *                         @OA\Property(property="barcode", type="string", example="BAR123456", description="Item barcode for scanning"),
     *                         @OA\Property(property="quantity", type="integer", example=2),
     *                         @OA\Property(property="temperature_requirement", type="string", example="refrigerated"),
     *                         @OA\Property(property="dropoff", type="object", description="Dropoff location for this specific item",
     *                             @OA\Property(property="name", type="string", example="Lab Station B"),
     *                             @OA\Property(property="address", type="string", example="456 Medical Plaza"),
     *                             @OA\Property(property="city", type="string", example="New York"),
     *                             @OA\Property(property="state", type="string", example="NY"),
     *                             @OA\Property(property="zip", type="string", example="10002"),
     *                             @OA\Property(property="phone", type="string", example="+1-555-0199"),
     *                             @OA\Property(property="contact_person", type="string", example="Dr. Smith"),
     *                             @OA\Property(property="location", type="object",
     *                                 @OA\Property(property="latitude", type="number", example=40.7128),
     *                                 @OA\Property(property="longitude", type="number", example=-74.0060)
     *                             )
     *                         ),
     *                         @OA\Property(property="status", type="string", example="pending")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Delivery not found"),
     *     @OA\Response(response=403, description="Access denied")
     * )
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $driverProfile = $user->driverProfile;
        $query = Delivery::with(['driver', 'creator', 'items.specimenType','items.tempratureRequirement','items.hospital', 'verifications','vehicleRequirement']);
        
        // Filter by user role
        if ($user->role === 'driver') {
            $query->where('driver_id', $user->id)->where('created_by',  $driverProfile->created_by);
        }

        $delivery = $query->find($id);
        if (!$delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery not found or access denied'
            ], 404);
        }
        // return $delivery->items->toArray();
        // echo '<pre>';print_r($delivery->toArray());die;
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $delivery->id,
                'delivery_number' => $delivery->delivery_number,
                'status' => $delivery->status,
                'priority' => $delivery->priority,
                'pickup' => [
                    'name' => $delivery->pickup_name,
                    'address' => $delivery->pickup_address,
                    'city' => $delivery->pickup_city,
                    'state' => $delivery->pickup_state,
                    'zip' => $delivery->pickup_zip,
                    'phone' => $delivery->pickup_phone,
                    'contact_person' => $delivery->pickup_contact_person,
                    'scheduled_time' => $delivery->pickup_scheduled_time,
                    'actual_time' => $delivery->pickup_actual_time,
                    'location' => [
                        'latitude' => $delivery->pickup_latitude,
                        'longitude' => $delivery->pickup_longitude
                    ]
                ],
                'delivery' => [
                    'name' => $delivery->delivery_name,
                    'address' => $delivery->delivery_address,
                    'city' => $delivery->delivery_city,
                    'state' => $delivery->delivery_state,
                    'zip' => $delivery->delivery_zip,
                    'phone' => $delivery->delivery_phone,
                    'contact_person' => $delivery->delivery_contact_person,
                    'scheduled_time' => $delivery->delivery_scheduled_time,
                    'actual_time' => $delivery->delivery_actual_time,
                    'requires_pickup_barcode_scan' => $delivery->requires_pickup_barcode_scan,
                    'requires_pickup_signature' => $delivery->requires_pickup_signature,
                    'requires_pickup_photo' => $delivery->requires_pickup_photo,
                    'requires_recepient_id_scan' => $delivery->requires_recepient_id_scan,
                    'requires_dropoff_signature' => $delivery->requires_dropoff_signature,
                    'requires_dropoff_barcode_scan' => $delivery->requires_dropoff_barcode_scan,
                    'requires_dropoff_photo' => $delivery->requires_dropoff_photo,
                    'location' => [
                        'latitude' => $delivery->delivery_latitude,
                        'longitude' => $delivery->delivery_longitude
                    ]
                ],
                'driver' => $delivery->driver ? [
                    'id' => $delivery->driver->id,
                    'name' => $delivery->driver->name,
                    'phone' => $delivery->driver->phone,
                    'email' => $delivery->driver->email
                ] : null,
                'temperature_requirement' => $delivery->temperature_requirement,
                'vehicle_requirements' => $delivery->vehicleRequirement ? $delivery->vehicleRequirement->name : null,
                'container_count' => $delivery->container_count,
                'items' => $delivery->items->map(function($item) {
                    return [
                        'id' => $item->id,
                        'item_name' => $item->item_name,
                        'item_type' => $item->item_type,
                        'specimen_type' => $item->specimenType->name ?? null,
                        'temperature_requirement' => $item->tempratureRequirement->name ?? null,
                        'item_code' => $item->item_code,
                        'quantity' => $item->quantity,
                        'description' => $item->description,
                        'requires_special_handling' => $item->requires_special_handling,
                        'handling_instructions' => $item->handling_instructions,
                        'dropoff' => [
                            'name' => $item->dropoff_name,
                            'address' => $item->dropoff_address,
                            'city' => $item->dropoff_city,
                            'state' => $item->dropoff_state,
                            'zip' => $item->dropoff_zip,
                            'phone' => $item->dropoff_phone,
                            'contact_person' => $item->dropoff_contact_person,
                            'location' => [
                                'latitude' => $item->dropoff_latitude,
                                'longitude' => $item->dropoff_longitude
                            ]
                        ],
                        'hospital' => $item->hospital,
                        'status' => $item->status
                    ];
                }),
                'item_count' => $delivery->items->count(),
                'verifications' => $delivery->verifications->map(function($verification) {
                    return [
                        'id' => $verification->id,
                        'verification_type' => $verification->verification_type,
                        'recipient_name' => $verification->recipient_name,
                        'verified_at' => $verification->verified_at,
                        'has_signature' => !empty($verification->signature_image),
                        'has_photo' => !empty($verification->photo_proof),
                        'location' => [
                            'latitude' => $verification->latitude,
                            'longitude' => $verification->longitude
                        ]
                    ];
                }),
                'distance_km' => $delivery->distance_km,
                'estimated_duration_minutes' => $delivery->estimated_duration_minutes,
                'special_instructions' => $delivery->special_instructions,
                'notes' => $delivery->notes,
                'created_by' => $delivery->creator ? [
                    'id' => $delivery->creator->id,
                    'name' => $delivery->creator->name
                ] : null,
                'created_at' => $delivery->created_at->toIso8601String(),
                'updated_at' => $delivery->updated_at->toIso8601String()
            ]
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/mobile/v1/deliveries/{id}/accept",
     *     summary="Accept delivery (driver en route to pickup)",
     *     description="Changes delivery status from 'assigned' to 'in_transit'. Returns full delivery details including items with barcodes, dropoff locations, temperature requirements, vehicle requirements, and container count",
     *     tags={"Mobile - Deliveries"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Delivery ID",
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
     *         description="Delivery accepted successfully with complete details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Delivery started successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=6),
     *                 @OA\Property(property="status", type="string", example="in_transit"),
     *                 @OA\Property(property="delivery_number", type="string", example="DEL-2026-0006"),
     *                 @OA\Property(property="temperature_requirement", type="string", example="refrigerated"),
     *                 @OA\Property(property="vehicle_requirements", type="string", example="refrigerated_van"),
     *                 @OA\Property(property="container_count", type="integer", example=3),
     *                 @OA\Property(property="items", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="barcode", type="string", example="BAR123456"),
     *                         @OA\Property(property="item_type", type="string"),
     *                         @OA\Property(property="temperature_requirement", type="string"),
     *                         @OA\Property(property="dropoff", type="object",
     *                             @OA\Property(property="name", type="string"),
     *                             @OA\Property(property="address", type="string"),
     *                             @OA\Property(property="location", type="object",
     *                                 @OA\Property(property="latitude", type="number"),
     *                                 @OA\Property(property="longitude", type="number")
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid status transition"),
     *     @OA\Response(response=404, description="Delivery not found")
     * )
     */
    public function acceptDelivery(Request $request, $id)
    {
        $user = $request->user();

        $delivery = Delivery::where('id', $id)
                           ->where('driver_id', $user->id)
                           ->with('items.specimenType','items.tempratureRequirement')
                           ->with('vehicleRequirement')
                           ->first();

        if (!$delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery not found or not assigned to you'
            ], 404);
        }
        if ($delivery->status !== 'assigned') {
            return response()->json([
                'success' => false,
                'message' => 'Delivery cannot be accepted from current status: ' . $delivery->status
            ], 400);
        }

        $delivery->status = 'accepted';
        $delivery->temperature_requirement = $request->temperature_reading;
        $delivery->notes = $request->notes;
        $delivery->save();

        //Send Notification to company
        $company = User::find($delivery->created_by);
        $title = "Job has been accepted by {$user->name}";
        $body = "A job has been accepted by {$user->name}. Please review the details.";
        SendFirebaseNotificationJob::dispatch(
            $company->device_token,
            $title,
            $body,
            'web',
            $delivery->created_by,
            [
                'type' => 'general',
                'user_id' => (string) $company->id,
                'delivery_id' => (string) $delivery->id
            ]
        );
        
        //Dispatch Event
        event(new DeliveryStatusUpdated($delivery));

        // Log activity
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'status_changed',
            'model_type' => 'App\Models\Delivery',
            'model_id' => $delivery->id,
            'description' => "Driver accepted delivery {$delivery->delivery_number}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        $delivery->refresh();
        return response()->json([
            'success' => true,
            'message' => 'Delivery accepted successfully',
            'data' => [
                'id' => $delivery->id,
                'delivery_number' => $delivery->delivery_number,
                'status' => $delivery->status,
                'priority' => $delivery->priority,
                'pickup' => [
                    'name' => $delivery->pickup_name,
                    'address' => $delivery->pickup_address,
                    'city' => $delivery->pickup_city,
                    'state' => $delivery->pickup_state,
                    'zip' => $delivery->pickup_zip,
                    'phone' => $delivery->pickup_phone,
                    'contact_person' => $delivery->pickup_contact_person,
                    'scheduled_time' => $delivery->pickup_scheduled_time,
                    'actual_time' => $delivery->pickup_actual_time,
                    'location' => [
                        'latitude' => $delivery->pickup_latitude,
                        'longitude' => $delivery->pickup_longitude
                    ]
                ],
                'delivery' => [
                    'name' => $delivery->delivery_name,
                    'address' => $delivery->delivery_address,
                    'city' => $delivery->delivery_city,
                    'state' => $delivery->delivery_state,
                    'zip' => $delivery->delivery_zip,
                    'phone' => $delivery->delivery_phone,
                    'contact_person' => $delivery->delivery_contact_person,
                    'scheduled_time' => $delivery->delivery_scheduled_time,
                    'actual_time' => $delivery->delivery_actual_time,
                    'requires_pickup_barcode_scan' => $delivery->requires_pickup_barcode_scan,
                    'requires_pickup_signature' => $delivery->requires_pickup_signature,
                    'requires_pickup_photo' => $delivery->requires_pickup_photo,
                    'requires_recepient_id_scan' => $delivery->requires_recepient_id_scan,
                    'requires_dropoff_signature' => $delivery->requires_dropoff_signature,
                    'requires_dropoff_barcode_scan' => $delivery->requires_dropoff_barcode_scan,
                    'requires_dropoff_photo' => $delivery->requires_dropoff_photo,
                    'location' => [
                        'latitude' => $delivery->delivery_latitude,
                        'longitude' => $delivery->delivery_longitude
                    ]
                ],
                'driver' => $delivery->driver ? [
                    'id' => $delivery->driver->id,
                    'name' => $delivery->driver->name,
                    'phone' => $delivery->driver->phone,
                    'email' => $delivery->driver->email
                ] : null,
                'temperature_requirement' => $delivery->temperature_requirement,
                'vehicle_requirements' => $delivery->vehicleRequirement ? $delivery->vehicleRequirement->name : null,
                'container_count' => $delivery->container_count,
                'items' => $delivery->items->map(function($item) {
                    return [
                        'id' => $item->id,
                        'item_name' => $item->item_name,
                        'item_type' => $item->item_type,
                        'specimen_type' => $item->specimenType->name ?? null,
                        'temperature_requirement' => $item->tempratureRequirement->name ?? null,
                        'item_code' => $item->item_code,
                        'quantity' => $item->quantity,
                        'requires_special_handling' => $item->requires_special_handling,
                        'handling_instructions' => $item->handling_instructions,
                        'description' => $item->description,
                        'dropoff' => [
                            'name' => $item->dropoff_name,
                            'address' => $item->dropoff_address,
                            'city' => $item->dropoff_city,
                            'state' => $item->dropoff_state,
                            'zip' => $item->dropoff_zip,
                            'phone' => $item->dropoff_phone,
                            'contact_person' => $item->dropoff_contact_person,
                            'location' => [
                                'latitude' => $item->dropoff_latitude,
                                'longitude' => $item->dropoff_longitude
                            ]
                        ],
                        'status' => $item->status
                    ];
                }),
                'item_count' => $delivery->items->count(),
                'verifications' => $delivery->verifications->map(function($verification) {
                    return [
                        'id' => $verification->id,
                        'verification_type' => $verification->verification_type,
                        'recipient_name' => $verification->recipient_name,
                        'verified_at' => $verification->verified_at,
                        'has_signature' => !empty($verification->signature_image),
                        'has_photo' => !empty($verification->photo_proof),
                        'location' => [
                            'latitude' => $verification->latitude,
                            'longitude' => $verification->longitude
                        ]
                    ];
                }),
                'distance_km' => $delivery->distance_km,
                'estimated_duration_minutes' => $delivery->estimated_duration_minutes,
                'special_instructions' => $delivery->special_instructions,
                'notes' => $delivery->notes,
                'created_by' => $delivery->creator ? [
                    'id' => $delivery->creator->id,
                    'name' => $delivery->creator->name
                ] : null,
                'created_at' => $delivery->created_at->toIso8601String(),
                'updated_at' => $delivery->updated_at->toIso8601String()
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/deliveries/{id}/start",
     *     summary="Start delivery (driver en route to pickup)",
     *     description="Changes delivery status from 'assigned' to 'in_transit'. Returns full delivery details including items with barcodes, dropoff locations, temperature requirements, vehicle requirements, and container count",
     *     tags={"Mobile - Deliveries"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Delivery ID",
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
     *         description="Delivery started successfully with complete details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Delivery started successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=6),
     *                 @OA\Property(property="status", type="string", example="in_transit"),
     *                 @OA\Property(property="delivery_number", type="string", example="DEL-2026-0006"),
     *                 @OA\Property(property="temperature_requirement", type="string", example="refrigerated"),
     *                 @OA\Property(property="vehicle_requirements", type="string", example="refrigerated_van"),
     *                 @OA\Property(property="container_count", type="integer", example=3),
     *                 @OA\Property(property="items", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="barcode", type="string", example="BAR123456"),
     *                         @OA\Property(property="item_type", type="string"),
     *                         @OA\Property(property="temperature_requirement", type="string"),
     *                         @OA\Property(property="dropoff", type="object",
     *                             @OA\Property(property="name", type="string"),
     *                             @OA\Property(property="address", type="string"),
     *                             @OA\Property(property="location", type="object",
     *                                 @OA\Property(property="latitude", type="number"),
     *                                 @OA\Property(property="longitude", type="number")
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid status transition"),
     *     @OA\Response(response=404, description="Delivery not found")
     * )
     */
    public function startDelivery(Request $request, $id)
    {
        $user = $request->user();

        $delivery = Delivery::where('id', $id)
                           ->where('driver_id', $user->id)
                           ->with('items.specimenType','items.tempratureRequirement')
                           ->with('vehicleRequirement')
                           ->first();
            
        if (!$delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery not found or not assigned to you'
            ], 404);
        }

        if ($delivery->status !== 'picked_up') {
            return response()->json([
                'success' => false,
                'message' => 'Delivery cannot be started from current status: ' . $delivery->status
            ], 400);
        }
        $delivery->status = 'in_transit';
        $delivery->temperature_requirement = $request->temperature_reading;
        $delivery->notes = $request->notes;
        $delivery->save();
        $delivery->refresh();
        // Log activity
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'status_changed',
            'model_type' => 'App\Models\Delivery',
            'model_id' => $delivery->id,
            'description' => "Driver started delivery {$delivery->delivery_number}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        //Send Notification to company
        $company = User::find($delivery->created_by);
        $title = "Job has been started by {$user->name}";
        $body = "A job has been started by {$user->name}. Please review the details.";
        SendFirebaseNotificationJob::dispatch(
            $company->device_token,
            $title,
            $body,
            'web',
            $delivery->created_by,
            [
                'type' => 'general',
                'user_id' => (string) $company->id,
                'delivery_id' => (string) $delivery->id
            ]
        );
        event(new DeliveryStatusUpdated($delivery));
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $delivery->id,
                'delivery_number' => $delivery->delivery_number,
                'status' => $delivery->status,
                'priority' => $delivery->priority,
                'pickup' => [
                    'name' => $delivery->pickup_name,
                    'address' => $delivery->pickup_address,
                    'city' => $delivery->pickup_city,
                    'state' => $delivery->pickup_state,
                    'zip' => $delivery->pickup_zip,
                    'phone' => $delivery->pickup_phone,
                    'contact_person' => $delivery->pickup_contact_person,
                    'scheduled_time' => $delivery->pickup_scheduled_time,
                    'actual_time' => $delivery->pickup_actual_time,
                    'location' => [
                        'latitude' => $delivery->pickup_latitude,
                        'longitude' => $delivery->pickup_longitude
                    ]
                ],
                'delivery' => [
                    'name' => $delivery->delivery_name,
                    'address' => $delivery->delivery_address,
                    'city' => $delivery->delivery_city,
                    'state' => $delivery->delivery_state,
                    'zip' => $delivery->delivery_zip,
                    'phone' => $delivery->delivery_phone,
                    'contact_person' => $delivery->delivery_contact_person,
                    'scheduled_time' => $delivery->delivery_scheduled_time,
                    'actual_time' => $delivery->delivery_actual_time,
                    'requires_pickup_barcode_scan' => $delivery->requires_pickup_barcode_scan,
                    'requires_pickup_signature' => $delivery->requires_pickup_signature,
                    'requires_pickup_photo' => $delivery->requires_pickup_photo,
                    'requires_recepient_id_scan' => $delivery->requires_recepient_id_scan,
                    'requires_dropoff_signature' => $delivery->requires_dropoff_signature,
                    'requires_dropoff_barcode_scan' => $delivery->requires_dropoff_barcode_scan,
                    'requires_dropoff_photo' => $delivery->requires_dropoff_photo,
                    'location' => [
                        'latitude' => $delivery->delivery_latitude,
                        'longitude' => $delivery->delivery_longitude
                    ]
                ],
                'driver' => $delivery->driver ? [
                    'id' => $delivery->driver->id,
                    'name' => $delivery->driver->name,
                    'phone' => $delivery->driver->phone,
                    'email' => $delivery->driver->email
                ] : null,
                'temperature_requirement' => $delivery->temperature_requirement,
                'vehicle_requirements' => $delivery->vehicleRequirement ? $delivery->vehicleRequirement->name : null,
                'container_count' => $delivery->container_count,
                'items' => $delivery->items->map(function($item) {
                    return [
                        'id' => $item->id,
                        'item_name' => $item->item_name,
                        'item_type' => $item->item_type,
                        'specimen_type' => $item->specimenType->name ?? null,
                        'temperature_requirement' => $item->tempratureRequirement->name ?? null,
                        'item_code' => $item->item_code,
                        'quantity' => $item->quantity,
                        'requires_special_handling' => $item->requires_special_handling,
                        'handling_instructions' => $item->handling_instructions,
                        'description' => $item->description,
                        'dropoff' => [
                            'name' => $item->dropoff_name,
                            'address' => $item->dropoff_address,
                            'city' => $item->dropoff_city,
                            'state' => $item->dropoff_state,
                            'zip' => $item->dropoff_zip,
                            'phone' => $item->dropoff_phone,
                            'contact_person' => $item->dropoff_contact_person,
                            'location' => [
                                'latitude' => $item->dropoff_latitude,
                                'longitude' => $item->dropoff_longitude
                            ]
                        ],
                        'status' => $item->status
                    ];
                }),
                'item_count' => $delivery->items->count(),
                'verifications' => $delivery->verifications->map(function($verification) {
                    return [
                        'id' => $verification->id,
                        'verification_type' => $verification->verification_type,
                        'recipient_name' => $verification->recipient_name,
                        'verified_at' => $verification->verified_at,
                        'has_signature' => !empty($verification->signature_image),
                        'has_photo' => !empty($verification->photo_proof),
                        'location' => [
                            'latitude' => $verification->latitude,
                            'longitude' => $verification->longitude
                        ]
                    ];
                }),
                'distance_km' => $delivery->distance_km,
                'estimated_duration_minutes' => $delivery->estimated_duration_minutes,
                'special_instructions' => $delivery->special_instructions,
                'notes' => $delivery->notes,
                'created_by' => $delivery->creator ? [
                    'id' => $delivery->creator->id,
                    'name' => $delivery->creator->name
                ] : null,
                'created_at' => $delivery->created_at->toIso8601String(),
                'updated_at' => $delivery->updated_at->toIso8601String()
            ]
        ]);
        
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/deliveries/{id}/pickup",
     *     summary="Confirm pickup with signature and item barcodes",
     *     description="Confirms package pickup with recipient signature, optional photo proof, and barcode scanning for multiple items",
     *     tags={"Mobile - Deliveries"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Delivery ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"recipient_name", "signature_image"},
     *             @OA\Property(property="recipient_name", type="string", example="John Doe"),
     *             @OA\Property(property="signature_image", type="string", description="Base64 encoded signature image"),
     *             @OA\Property(property="photo_proof", type="string", description="Base64 encoded photo"),
     *             @OA\Property(property="latitude", type="number", format="float", example=40.7580),
     *             @OA\Property(property="longitude", type="number", format="float", example=-73.9855),
     *             @OA\Property(property="notes", type="string", example="Package in good condition"),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 description="Array of items with barcodes for scanning",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"item_id", "barcode"},
     *                     @OA\Property(property="item_id", type="integer", example=1),
     *                     @OA\Property(property="barcode", type="string", example="BAR123456"),
     *                     @OA\Property(property="scanned_at", type="string", format="date-time", example="2026-03-30T10:30:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pickup confirmed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function confirmPickup(Request $request, $id)
    {
        $user = $request->user();

        $delivery = Delivery::where('id', $id)
                           ->where('driver_id', $user->id)
                           ->with('items')
                           ->first();        
        // echo '<pre>';print_r($delivery->toArray());die;
        if (!$delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery not found or not assigned to you'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            //'recipient_name' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'notes' => 'nullable|string|max:1000',
            'items' => 'nullable|array',
            'items.*.item_id' => 'required|integer|exists:delivery_items,id',
            //'items.*.barcode' => 'required|string|max:255',
            //'items.*.scanned_at' => 'nullable|date',
            //'items.*.signature_image' => 'required|string',
            //'items.*.photo_proof' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        $check = Delivery::where('driver_id',auth()->id())
        ->whereIn('status', ['picked_up', 'in_transit'])
        ->first();
        if($check){
            $status = str_replace('_', '-', $check->status);
            return $this->errorResponse("You already have a {$status} job", 200);
        }
        DB::beginTransaction();
        try {
            // Create pickup verification
            DeliveryVerification::create([
                'delivery_id' => $delivery->id,
                'verification_type' => 'pickup',
                'recipient_name' => '',
                'signature_image' => '',
                'photo_proof' => $request->photo_proof,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'notes' => $request->notes,
                'verified_at' => now()
            ]);
            // Update barcodes for scanned items
            if ($request->has('items') && is_array($request->items)) {
                
                foreach ($request->items as $scannedItem) {
                    $item = DeliveryItem::where('id', $scannedItem['item_id'])
                                       ->where('delivery_id', $delivery->id)
                                       ->first();
                    // print_r($item);die;
                    if ($item) {
                        $item->barcode = $scannedItem['barcode'];
                        //$item->recipient_name = $scannedItem['recipient_name'];
                        $item->signature_image = $scannedItem['signature_image'];
                        $item->photo_proof = $scannedItem['photo_proof'];
                        $item->notes = $scannedItem['notes'];
                        //$item->scanned_at = $scannedItem['scanned_at'];
                        $item->status = 'collected';
                        $item->save();
                    }
                }
            }

            // Update delivery status
            $delivery->status = 'picked_up';
            $delivery->pickup_actual_time = now();
            $delivery->save();

            // Update remaining items status (items not scanned)
            DeliveryItem::where('delivery_id', $delivery->id)
                        ->where('status', 'pending')
                        ->update(['status' => 'collected']);

            // Log activity
            $itemsScanned = $request->has('items') ? count($request->items) : 0;
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'verified',
                'model_type' => 'App\Models\Delivery',
                'model_id' => $delivery->id,
                'description' => "Pickup confirmed for delivery {$delivery->delivery_number} ({$itemsScanned} items scanned)",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            DB::commit();

            //Send Notification to company
            $company = User::find($delivery->created_by);
            $title = "Job has been picked up by {$user->name}";
            $body = "A job has been picked up by {$user->name}. Please review the details.";
            SendFirebaseNotificationJob::dispatch(
                $company->device_token,
                $title,
                $body,
                'web',
                $delivery->created_by,
                [
                    'type' => 'general',
                    'user_id' => (string) $company->id,
                    'delivery_id' => (string) $delivery->id
                ]
            );
            event(new DeliveryStatusUpdated($delivery));

            return response()->json([
                'success' => true,
                'message' => 'Pickup confirmed successfully',
                'data' => [
                    'item_id' => $delivery->id,
                    'status' => $delivery->status,
                    'pickup_actual_time' => $delivery->pickup_actual_time->toIso8601String(),
                    'items_scanned' => $itemsScanned,
                    'total_items' => $delivery->items->count()
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm pickup',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/deliveries/{id}/complete",
     *     summary="Complete delivery with signature",
     *     description="Completes delivery with recipient signature and optional photo proof",
     *     tags={"Mobile - Deliveries"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Delivery ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"recipient_name", "signature_image"},
     *             @OA\Property(property="recipient_name", type="string", example="Jane Smith"),
     *             @OA\Property(property="signature_image", type="string", description="Base64 encoded signature image"),
     *             @OA\Property(property="photo_proof", type="string", description="Base64 encoded photo"),
     *             @OA\Property(property="latitude", type="number", format="float", example=40.7314),
     *             @OA\Property(property="longitude", type="number", format="float", example=-73.9870),
     *             @OA\Property(property="notes", type="string", example="Delivered to reception")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Delivery completed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function completeDelivery(Request $request, $id)
    {
        $user = $request->user();
        $delivery = Delivery::where('id', $id)
                           ->where('driver_id', $user->id)
                           ->with('items')
                           ->first();
        // echo '<pre>';print_r($delivery->toArray());die;
        if (!$delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery not found or not assigned to you'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            //'recipient_name' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'notes' => 'nullable|string|max:1000',
            'items' => 'nullable|array',
            'items.*.item_id' => 'required|integer|exists:delivery_items,id',
            //'items.*.barcode' => 'required|string|max:255',
            //'items.*.scanned_at' => 'nullable|date',
            //'items.*.signature_image' => 'required|string',
            //'items.*.photo_proof' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            
            // Update barcodes for scanned items
            if ($request->has('items') && is_array($request->items)) {
                
                foreach ($request->items as $scannedItem) {
                    $item = DeliveryItem::where('id', $scannedItem['item_id'])
                                       ->where('delivery_id', $delivery->id)
                                       ->first();
                    // print_r($item);die;
                    if ($item) {
                        if($scannedItem['barcode'] != '' && $scannedItem['barcode'] != $item->barcode){
                            return response()->json([
                                'success' => false,
                                'message' => "Barcode mismatch for item name: {$item->item_name}"
                            ], 400);
                        }
                        $item->barcode = $scannedItem['barcode'];
                        //$item->recipient_name = $scannedItem['recipient_name'];
                        $item->signature_image = $scannedItem['signature_image'];
                        $item->photo_proof = $scannedItem['photo_proof'];
                        $item->notes = $scannedItem['notes'];
                        //$item->scanned_at = $scannedItem['scanned_at'];
                        $item->status = 'collected';
                        $item->save();
                    }
                }
            }

            // Create pickup verification
            DeliveryVerification::create([
                'delivery_id' => $delivery->id,
                'verification_type' => 'pickup',
                'recipient_name' => '',
                'signature_image' => '',
                'photo_proof' => $request->photo_proof,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'notes' => $request->notes,
                'verified_at' => now()
            ]);

            $delivery->status = 'delivered';
            $delivery->delivery_actual_time = now();
            $delivery->save();

            // Update delivery items status
            DeliveryItem::where('delivery_id', $delivery->id)
                        ->whereNotIn('status', ['cancelled'])
                        ->update(['status' => 'delivered']);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'status_changed',
                'model_type' => 'App\Models\Delivery',
                'model_id' => $delivery->id,
                'description' => "Delivery {$delivery->delivery_number} completed",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            DB::commit();

            //Send Notification to company
            $company = User::find($delivery->created_by);
            $title = "Job has been delivered by {$user->name}";
            $body = "A job has been delivered by {$user->name}. Please review the details.";
            SendFirebaseNotificationJob::dispatch(
                $company->device_token,
                $title,
                $body,
                'web',
                $delivery->created_by,
                [
                    'type' => 'general',
                    'user_id' => (string) $company->id,
                    'delivery_id' => (string) $delivery->id
                ]
            );

            event(new DeliveryStatusUpdated($delivery));
            return response()->json([
                'success' => true,
                'message' => 'Delivery completed successfully',
                'data' => [
                    'id' => $delivery->id,
                    'status' => $delivery->status,
                    'delivery_actual_time' => $delivery->delivery_actual_time->toIso8601String()
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete delivery',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/mobile/v1/deliveries/{id}/failed",
     *     summary="Failed delivery with notes",
     *     description="Failed delivery with driver notes",
     *     tags={"Mobile - Deliveries"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Delivery ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type"},
     *             @OA\Property(property="type", type="string", example="delivery or item")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Delivery failed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function failedDelivery(Request $request, $id)
    {
        
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:delivery,item'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $delivery = Delivery::where('id', $id)
                           ->where('driver_id', $user->id)
                           ->first();

        DB::beginTransaction();
        try {
            if($request->type == 'delivery'){
                if (!$delivery) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Delivery not found or not assigned to you'
                    ], 404);
                }
        
                // Update delivery status
                $delivery->status = 'failed';
                $delivery->notes = $request->notes;
                $delivery->delivery_actual_time = now();
                $delivery->save();

                // Log activity
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'status_changed',
                    'model_type' => 'App\Models\Delivery',
                    'model_id' => $delivery->id,
                    'description' => "Delivery {$delivery->delivery_number} failed",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Delivery failed successfully',
                    'data' => [
                        'id' => $delivery->id,
                        'status' => $delivery->status,
                        'delivery_actual_time' => $delivery->delivery_actual_time->toIso8601String()
                    ]
                ]);
            }else{
                // Update delivery items status
                $delivery_item = DeliveryItem::where('id', $request->item_id)
                                               ->where('delivery_id', $id)->first();
                if (!$delivery_item) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Delivery Item not found or not assigned with your job'
                    ], 404);
                }
                $delivery_item->status = 'failed';
                $delivery_item->notes = $request->notes;
                $delivery_item->save();

                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'status_changed',
                    'model_type' => 'App\Models\DeliveryItem',
                    'model_id' => $request->item_id,
                    'description' => "Delivery failed",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                DB::commit();

                //Send Notification to company
                $company = User::find($delivery->created_by);
                $title = "Job has been accepted by {$user->name}";
                $body = "A job has been accepted by {$user->name}. Please review the details.";
                SendFirebaseNotificationJob::dispatch(
                    $company->device_token,
                    $title,
                    $body,
                    'web',
                    $delivery->created_by,
                    [
                        'type' => 'general',
                        'user_id' => (string) $company->id,
                        'delivery_id' => (string) $delivery->id
                    ]
                );

                event(new DeliveryStatusUpdated($delivery));

                return response()->json([
                    'success' => true,
                    'message' => 'Delivery Item failed successfully',
                    'data' => [
                        'id' => $request->item_id,
                        'status' => 'failed',
                        //'delivery_actual_time' => $delivery->delivery_actual_time->toIso8601String()
                    ]
                ]);
            }

        
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete delivery',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/deliveries/my-active",
     *     summary="Get driver's active deliveries",
     *     description="Returns all active deliveries (assigned, in_transit, picked_up) for the current driver",
     *     tags={"Mobile - Deliveries"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Active deliveries",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function myActiveDeliveries(Request $request)
    {
        $user = $request->user();
        $driverProfile = $user->driverProfile;
        // echo '<pre>';print_r($user->toArray());die; 
        if ($user->role !== 'driver') {
            return response()->json([
                'success' => false,
                'message' => 'This endpoint is only for drivers'
            ], 403);
        }

        $deliveries = Delivery::query()
        ->select([
            'id',
            'delivery_number',
            'status',
            'priority',

            'pickup_name',
            'pickup_address',
            'pickup_city',
            'pickup_phone',
            'pickup_scheduled_time',
            'pickup_latitude',
            'pickup_longitude',

            'delivery_name',
            'delivery_address',
            'delivery_city',
            'delivery_phone',
            'delivery_scheduled_time',

            'requires_pickup_barcode_scan',
            'requires_pickup_signature',
            'requires_pickup_photo',
            'requires_recepient_id_scan',
            'requires_dropoff_signature',
            'requires_dropoff_barcode_scan',
            'requires_dropoff_photo',

            'delivery_latitude',
            'delivery_longitude',

            'distance_km',
            'estimated_duration_minutes',
            'special_instructions',
        ])
        ->where('driver_id', $user->id)
        ->where('created_by', $driverProfile->created_by)
        ->whereIn('status', ['in_transit', 'accepted', 'picked_up'])
        ->withCount('items')
        ->orderBy('pickup_scheduled_time')
        ->get();

        $transformedDeliveries = $deliveries->map(function($delivery) {
            return [
                'id' => $delivery->id,
                'delivery_number' => $delivery->delivery_number,
                'status' => $delivery->status,
                'priority' => $delivery->priority,
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
                    'requires_pickup_barcode_scan' => $delivery->requires_pickup_barcode_scan,
                    'requires_pickup_signature' => $delivery->requires_pickup_signature,
                    'requires_pickup_photo' => $delivery->requires_pickup_photo,
                    'requires_recepient_id_scan' => $delivery->requires_recepient_id_scan,
                    'requires_dropoff_signature' => $delivery->requires_dropoff_signature,
                    'requires_dropoff_barcode_scan' => $delivery->requires_dropoff_barcode_scan,
                    'requires_dropoff_photo' => $delivery->requires_dropoff_photo,
                    'location' => [
                        'latitude' => $delivery->delivery_latitude,
                        'longitude' => $delivery->delivery_longitude
                    ]
                ],
                'item_count' => $delivery->items->count(),
                'distance_km' => $delivery->distance_km,
                'estimated_duration_minutes' => $delivery->estimated_duration_minutes,
                'special_instructions' => $delivery->special_instructions
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $transformedDeliveries
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/deliveries/history",
     *     summary="Get delivery history with pagination",
     *     description="Returns paginated history of completed and cancelled deliveries",
     *     tags={"Mobile - Deliveries"},
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
     *     @OA\Response(
     *         response=200,
     *         description="Delivery history",
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
        $driverProfile = $user->driverProfile;
        $perPage = min($request->input('per_page', 20), 100);

        $query = Delivery::query();

        if ($user->role === 'driver') {
            $query->where('driver_id', $user->id)->where('created_by', $driverProfile->created_by);
        }

        $deliveries = $query->whereIn('status', ['delivered', 'cancelled', 'failed'])
                            ->with('items:id,delivery_id,item_type,quantity')
                            ->orderBy('updated_at', 'desc')
                            ->paginate($perPage);

        $transformedDeliveries = $deliveries->map(function($delivery) {
            return [
                'id' => $delivery->id,
                'delivery_number' => $delivery->delivery_number,
                'status' => $delivery->status,
                'priority' => $delivery->priority,
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
                    'requires_pickup_barcode_scan' => $delivery->requires_pickup_barcode_scan,
                    'requires_pickup_signature' => $delivery->requires_pickup_signature,
                    'requires_pickup_photo' => $delivery->requires_pickup_photo,
                    'requires_recepient_id_scan' => $delivery->requires_recepient_id_scan,
                    'requires_dropoff_signature' => $delivery->requires_dropoff_signature,
                    'requires_dropoff_barcode_scan' => $delivery->requires_dropoff_barcode_scan,
                    'requires_dropoff_photo' => $delivery->requires_dropoff_photo,
                    'location' => [
                        'latitude' => $delivery->delivery_latitude,
                        'longitude' => $delivery->delivery_longitude
                    ]
                ],
                'item_count' => $delivery->items->count(),
                'distance_km' => $delivery->distance_km,
                'estimated_duration_minutes' => $delivery->estimated_duration_minutes,
                'special_instructions' => $delivery->special_instructions
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'deliveries' => $transformedDeliveries,
                'pagination' => [
                    'total' => $deliveries->total(),
                    'per_page' => $deliveries->perPage(),
                    'current_page' => $deliveries->currentPage(),
                    'last_page' => $deliveries->lastPage(),
                    'from' => $deliveries->firstItem(),
                    'to' => $deliveries->lastItem()
                ]
            ]
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/mobile/v1/temperature/update",
     *     summary="Update Temperature",
     *     description="",
     *     tags={"Mobile - Deliveries"},
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
     *     @OA\Response(
     *         response=200,
     *         description="Delivery history",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    function temperatureUpdate(Request $request,$id){
        $user = $request->user();
        $request->validate([
            'reading' => 'required',
        ]);
        $delivery = Delivery::where('id', $id)
                           ->where('driver_id', $user->id)
                           ->first();
        if(!$delivery){
            if (!$delivery) {
                return response()->json([
                    'success' => false,
                    'message' => 'Delivery not found or not assigned to you'
                ], 404);
            }
        }

        $delivery->temperature_reading = $request->reading;
        $delivery->notes = $request->notes;
        $delivery->save();
        return response()->json([
            'success' => true,
            'message' => 'Temperature updated successfully.',
        ]);
    }

}
