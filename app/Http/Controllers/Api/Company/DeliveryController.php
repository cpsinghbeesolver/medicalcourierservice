<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\DeliveryVerification;
use App\Models\ActivityLog;
use App\Http\Resources\DeliveryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\SpecimenType;
use App\Models\TemperatureRequirement;
use App\Models\VehicleRequirement;
use App\Http\Requests\StoreSpecimenTempVehicleRequest;
use App\Services\FirebaseService;
use App\Models\User;
use App\Models\CompanyHospital;
use App\Jobs\SendFirebaseNotificationJob;

class DeliveryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/deliveries",
     *     summary="Get list of deliveries",
     *     tags={"Deliveries"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pending","assigned","in_transit","picked_up","delivered","failed","cancelled"})
     *     ),
     *     @OA\Parameter(
     *         name="driver_id",
     *         in="query",
     *         description="Filter by driver ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="priority",
     *         in="query",
     *         description="Filter by priority",
     *         required=false,
     *         @OA\Schema(type="string", enum={"low","normal","high","urgent"})
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deliveries retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Deliveries retrieved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {   
        //check if user is superadmin
        if(Auth::user()->role_id == 1){
            $query = Delivery::with(['driver', 'creator', 'items','vehicleRequirement']);    
        } else {
            // dd('here');
            $query = Delivery::with([
                'driver',
                'creator',
                'items' => function ($query) {
                    $query->with('specimenType:id,name');
                },
                'vehicleRequirement'
            ]);
            // $query = Delivery::with(['driver', 'creator', 'items'])->where('created_by', Auth::id());
        }
        if(Auth::user()->isAdmin()){
            $query->where('created_by', Auth::id());
        }
        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Date range filter
        if ($request->has('from_date')) {
            $query->whereDate('pickup_scheduled_time', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('pickup_scheduled_time', '<=', $request->to_date);
        }

        // Search by delivery number
        if ($request->has('search')) {
            $query->where('delivery_number', 'LIKE', '%' . $request->search . '%');
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $perPage = $request->get('per_page');
        if ($request->has('page') || $perPage) {
            $perPage = $perPage ? (int) $perPage : 15;
            $deliveries = $query->paginate($perPage);
            // dd($deliveries->toArray());
            return $this->successResponse([
                'deliveries' => DeliveryResource::collection($deliveries),
                'pagination' => [
                    'total' => $deliveries->total(),
                    'per_page' => $deliveries->perPage(),
                    'current_page' => $deliveries->currentPage(),
                    'last_page' => $deliveries->lastPage(),
                    'from' => $deliveries->firstItem(),
                    'to' => $deliveries->lastItem(),
                ]
            ], 'Deliveries retrieved successfully');
        }

        $deliveries = $query->get();

        return $this->successResponse([
            'deliveries' => DeliveryResource::collection($deliveries),
        ], 'Deliveries retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/deliveries",
     *     summary="Create a new delivery",
     *     tags={"Deliveries"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"pickup_name","pickup_address","pickup_city","pickup_state","pickup_zip","pickup_phone","pickup_scheduled_time","delivery_name","delivery_address","delivery_city","delivery_state","delivery_zip","delivery_phone","delivery_scheduled_time","priority"},
     *             @OA\Property(property="pickup_name", type="string", example="City Hospital"),
     *             @OA\Property(property="pickup_address", type="string", example="123 Main St"),
     *             @OA\Property(property="pickup_city", type="string", example="New York"),
     *             @OA\Property(property="pickup_state", type="string", example="NY"),
     *             @OA\Property(property="pickup_zip", type="string", example="10001"),
     *             @OA\Property(property="pickup_phone", type="string", example="+1234567890"),
     *             @OA\Property(property="pickup_contact_person", type="string", example="John Doe"),
     *             @OA\Property(property="pickup_scheduled_time", type="string", format="date-time", example="2026-03-10T10:00:00Z"),
     *             @OA\Property(property="delivery_name", type="string", example="Medical Lab"),
     *             @OA\Property(property="delivery_address", type="string", example="456 Oak Ave"),
     *             @OA\Property(property="delivery_city", type="string", example="New York"),
     *             @OA\Property(property="delivery_state", type="string", example="NY"),
     *             @OA\Property(property="delivery_zip", type="string", example="10002"),
     *             @OA\Property(property="delivery_phone", type="string", example="+0987654321"),
     *             @OA\Property(property="delivery_contact_person", type="string", example="Jane Smith"),
     *             @OA\Property(property="delivery_scheduled_time", type="string", format="date-time", example="2026-03-10T14:00:00Z"),
     *             @OA\Property(property="priority", type="string", enum={"low","normal","high","urgent"}, example="high"),
     *             @OA\Property(property="special_instructions", type="string", example="Handle with care"),
     *             @OA\Property(property="items", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="item_type", type="string", example="specimen"),
     *                     @OA\Property(property="specimen_type", type="string", example="blood"),
     *                     @OA\Property(property="quantity", type="integer", example=2),
     *                     @OA\Property(property="temperature_requirement", type="string", example="refrigerated")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Delivery created successfully"
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request, FirebaseService $firebase)
    {
        $validator = Validator::make($request->all(), [
            // PHI - Patient/Specimen Information
            'job_title' => 'required|string|max:255',
            'urgency_level' => 'required|in:routine,stat,life_threatening',

            // Pickup Information
            'pickup_name' => 'required|string|max:255',
            'pickup_address' => 'required|string',
            'pickup_phone' => 'required|integer',
            'pickup_contact_person' => 'nullable|string|max:255',
            'pickup_latitude' => 'nullable|numeric|between:-90,90',
            'pickup_longitude' => 'nullable|numeric|between:-180,180',

            // Time Window
            'scheduled_time_window_start' => 'required|date',
            'scheduled_time_window_end' => 'required|date|after:scheduled_time_window_start',

            // Delivery Information
            'container_count' => 'nullable|integer|max:1000',

            'priority' => 'required|in:low,normal,high,urgent',

            // Driver Assignment and Vehicle
            'driver_id' => 'nullable|exists:users,id',

            // Special Instructions
            'special_instructions' => 'nullable|string',
            'notes' => 'nullable|string',

            // Digital Chain of Custody
            'requires_barcode_scan' => 'nullable|boolean',
            'requires_pickup_photo' => 'nullable|boolean',
            'requires_pickup_signature' => 'nullable|boolean',
            'requires_recepient_id_scan' => 'nullable|boolean',
            'requires_dropoff_signature' => 'nullable|boolean',
            'requires_dropoff_barcode_scan' => 'nullable|boolean',
            'requires_dropoff_photo' => 'nullable|boolean',


            // Items with temperature requirements
            'items' => 'nullable|array',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.handling_instructions' => 'nullable|string',
            'items.*.dropoff_location' => 'nullable|string',
            'items.*.dropoff_phone' => 'nullable|string|max:20',
            'items.*.dropoff_latitude' => 'nullable|numeric|between:-90,90',
            'items.*.dropoff_longitude' => 'nullable|numeric|between:-180,180',
            'items.*.description' => 'nullable|string',
            'items.*.dropoff_contact_person' => 'nullable|string|max:255',
            // 'items.*.dropoff_address' => 'required|nullable|string',
            'items.*.item_type' => 'required|string',
            'items.*.item_code' => 'required|nullable|string',
            'items.*.specimen_type' => 'nullable|string',
            'items.*.dropoff_address' => [
                'nullable',
                'string',
                'required_if:items.*.dropoff_type,address',
            ],

            'items.*.hospital_id' => [
                'nullable',
                'required_if:items.*.dropoff_type,hospital',
            ],
        ], [
            'items.*.item_name.required' => 'Item name is required.',
            'items.*.dropoff_address.required_if' => 'Dropoff Address is required when Dropoff type is Address.',
            'items.*.hospital_id.required_if' => 'Please select a Hospital when Dropoff type is Hospital.',
        ]);
        
        //Check if Proof of Pickup is selected
        $validator->after(function ($validator) use ($request) {

            $fields = [
                'requires_pickup_barcode_scan',
                'requires_pickup_photo',
                'requires_pickup_signature'
            ];

            $atLeastOneSelected = collect($fields)
                ->contains(fn ($field) => $request->boolean($field));

            if (! $atLeastOneSelected) {
                $validator->errors()->add(
                    'chain_of_custody',
                    'At least one Proof of Pickup must be selected.'
                );
            }
        });

        //Check if Proof of Delivery is selected
        $validator->after(function ($validator) use ($request) {

            $fields = [
                'requires_recepient_id_scan',
                'requires_dropoff_signature',
                'requires_dropoff_barcode_scan',
                'requires_dropoff_photo',
            ];

            $atLeastOneSelected = collect($fields)
                ->contains(fn ($field) => $request->boolean($field));

            if (! $atLeastOneSelected) {
                $validator->errors()->add(
                    'chain_of_custody',
                    'At least one Proof of Delivery must be selected.'
                );
            }
        });

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors()->all());
        }
        // dd($request->all());
        $request->pickup_city = '';
        $request->pickup_state = '';
        $request->pickup_zip = '';
        $request->pickup_scheduled_time = '';
        $request->pickup_state = '';
        $request->pickup_state = '';
        $request->pickup_state = '';
        $request->pickup_state = '';

        
        DB::beginTransaction();
        try {
            // Determine status based on driver assignment
            $status = $request->driver_id ? 'assigned' : 'pending';

            // Create delivery
            $delivery = Delivery::create([
                'created_by' => $request->user()->id,

                // PHI - Protected Health Information
                'specimen_id' => $request->specimen_id,
                'patient_initials' => $request->patient_initials,
                'urgency_level' => $request->urgency_level,

                // Pickup Information
                'pickup_name' => $request->pickup_name,
                'pickup_address' => $request->pickup_address,
                'pickup_city' => $request->pickup_city,
                'pickup_state' => $request->pickup_state,
                'pickup_zip' => $request->pickup_zip,
                'pickup_phone' => $request->pickup_phone,
                'pickup_contact_person' => $request->pickup_contact_person,
                'pickup_latitude' => $request->pickup_latitude,
                'pickup_longitude' => $request->pickup_longitude,
                'pickup_scheduled_time' => $request->scheduled_time_window_start,

                // Delivery Information
                'delivery_name' => '',
                'delivery_address' => '',
                // 'delivery_city' => $request->delivery_city,
                // 'delivery_state' => $request->delivery_state,
                // 'delivery_zip' => $request->delivery_zip,
                // 'delivery_phone' => $request->delivery_phone,
                // 'delivery_contact_person' => $request->delivery_contact_person,
                // 'delivery_latitude' => $request->delivery_latitude,
                // 'delivery_longitude' => $request->delivery_longitude,
                'delivery_scheduled_time' => $request->scheduled_time_window_end,

                // Time Window
                'scheduled_time_window_start' => $request->scheduled_time_window_start,
                'scheduled_time_window_end' => $request->scheduled_time_window_end,

                // Driver Assignment
                'driver_id' => $request->driver_id,
                'dispatched_at' => $request->driver_id ? now() : null,

                // Vehicle Requirements
                'required_vehicle_type' => $request->required_vehicle_type,

                // Digital Chain of Custody
                'requires_pickup_barcode_scan' => $request->requires_pickup_barcode_scan ?? false,
                'requires_pickup_signature' => $request->requires_pickup_signature ?? true,
                'requires_pickup_photo' => $request->requires_pickup_photo ?? false,
                'requires_recepient_id_scan' => $request->requires_recepient_id_scan ?? false,
                'requires_dropoff_signature' => $request->requires_dropoff_signature ?? false,
                'requires_dropoff_barcode_scan' => $request->requires_dropoff_barcode_scan ?? false,
                'requires_dropoff_photo' => $request->requires_dropoff_photo ?? false,
                'container_count' => $request->container_count ?? 1,
                // Other fields
                'priority' => $request->priority,
                'special_instructions' => $request->special_instructions,
                'notes' => $request->notes,
                'distance_km' => $request->distance_km,
                'estimated_duration_minutes' => $request->estimated_duration_minutes,
                'status' => $status,
            ]);
            // dd($delivery->id);
            // Create delivery items if provided
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $itemData) {
                    //$itemData['specimen_type'] = $request->specimen_type ?? null;
                    DeliveryItem::create(array_merge($itemData, [
                        'delivery_id' => $delivery->id,
                    ]));

                    // //Add hospital id with company id
                    // if($itemData->hospital_id){
                    //     CompanyHospital::create([
                    //         'hospital_id' => $itemData->hospital_id,
                    //         'company_id' => auth()->id()
                    //     ]);
                    // }
                }
            }
            

            // Track delivery usage for subscription
            if ($request->user()->subscription) {
                $request->user()->subscription->incrementUsage('deliveries', 1);
            }

            // Log activity
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'delivery_created',
                'model_type' => Delivery::class,
                'model_id' => $delivery->id,
                'description' => "Created delivery {$delivery->delivery_number}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            //Send Notification
            $driver = User::find($request->driver_id);
            if($driver){
                if($driver->device_token){
                    $title = "New Job Requested";
                    $body = "New Job has been Requested. Kindly accept it.";
                    SendFirebaseNotificationJob::dispatch(
                        $driver->device_token,
                        $title,
                        $body,
                        'mobile',
                        $request->driver_id,
                        [
                            'type' => 'general',
                            'user_id' => (string) $driver->id,
                            'delivery_id' => (string) $delivery->id
                        ]
                    );
                }
            }

            DB::commit();

            return $this->successResponse(
                new DeliveryResource($delivery->load(['items', 'creator'])),
                'Delivery created successfully',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create delivery: ' . $e->getMessage(), 500);
        }
    }


    
    public function editDelivery(Request $request, $delivery_id, FirebaseService $firebase)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            // PHI - Patient/Specimen Information
            'job_title' => 'required|string|max:255',
            'urgency_level' => 'required|in:routine,stat,life_threatening',

            // Pickup Information
            'pickup_name' => 'required|string|max:255',
            'pickup_address' => 'required|string',
            'pickup_phone' => 'required|integer',
            'pickup_contact_person' => 'nullable|string|max:255',
            'pickup_latitude' => 'nullable|numeric|between:-90,90',
            'pickup_longitude' => 'nullable|numeric|between:-180,180',

            // Time Window
            'scheduled_time_window_start' => 'required|date',
            'scheduled_time_window_end' => 'required|date|after:scheduled_time_window_start',

            // Delivery Information
            'container_count' => 'nullable|integer|max:1000',

            'priority' => 'required|in:low,normal,high,urgent',

            // Driver Assignment and Vehicle
            'driver_id' => 'nullable|exists:users,id',

            // Special Instructions
            'special_instructions' => 'nullable|string',
            'notes' => 'nullable|string',

            // Digital Chain of Custody
            'requires_barcode_scan' => 'nullable|boolean',
            'requires_pickup_photo' => 'nullable|boolean',
            'requires_pickup_signature' => 'nullable|boolean',
            'requires_recepient_id_scan' => 'nullable|boolean',
            'requires_dropoff_signature' => 'nullable|boolean',
            'requires_dropoff_barcode_scan' => 'nullable|boolean',
            'requires_dropoff_photo' => 'nullable|boolean',


            // Items with temperature requirements
            'items' => 'nullable|array',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.handling_instructions' => 'nullable|string',
            'items.*.dropoff_location' => 'nullable|string',
            'items.*.dropoff_phone' => 'nullable|string|max:20',
            'items.*.dropoff_latitude' => 'nullable|numeric|between:-90,90',
            'items.*.dropoff_longitude' => 'nullable|numeric|between:-180,180',
            'items.*.description' => 'nullable|string',
            'items.*.dropoff_contact_person' => 'nullable|string|max:255',
            // 'items.*.dropoff_address' => 'required|nullable|string',
            'items.*.item_type' => 'required|string',
            'items.*.item_code' => 'required|nullable|string',
            'items.*.specimen_type' => 'nullable|string',
            'items.*.dropoff_address' => [
                'nullable',
                'string',
                'required_if:items.*.dropoff_type,address',
            ],

            'items.*.hospital_id' => [
                'nullable',
                'required_if:items.*.dropoff_type,hospital',
            ],
        ], [
            'items.*.item_name.required' => 'Item name is required.',
            'items.*.dropoff_address.required_if' => 'Dropoff Address is required when Dropoff type is Address.',
            'items.*.hospital_id.required_if' => 'Please select a Hospital when Dropoff type is Hospital.',
        ]);

        //Check if Proof of Pickup is selected
        $validator->after(function ($validator) use ($request) {

            $fields = [
                'requires_pickup_barcode_scan',
                'requires_pickup_photo',
                'requires_pickup_signature'
            ];

            $atLeastOneSelected = collect($fields)
                ->contains(fn ($field) => $request->boolean($field));

            if (! $atLeastOneSelected) {
                $validator->errors()->add(
                    'chain_of_custody',
                    'At least one Proof of Pickup must be selected.'
                );
            }
        });

        //Check if Proof of Delivery is selected
        $validator->after(function ($validator) use ($request) {

            $fields = [
                'requires_recepient_id_scan',
                'requires_dropoff_signature',
                'requires_dropoff_barcode_scan',
                'requires_dropoff_photo',
            ];

            $atLeastOneSelected = collect($fields)
                ->contains(fn ($field) => $request->boolean($field));

            if (! $atLeastOneSelected) {
                $validator->errors()->add(
                    'chain_of_custody',
                    'At least one Proof of Delivery must be selected.'
                );
            }
        });


        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors()->all());
        }

        $delivery = Delivery::find($delivery_id);

        if (!$delivery) {
            return $this->errorResponse('Delivery not found.', 404);
        }

        DB::beginTransaction();

        try {

            $oldDriverId = $delivery->driver_id;

            $status = $request->driver_id ? 'assigned' : 'pending';

            $delivery->update([

                'specimen_id' => $request->specimen_id,
                'patient_initials' => $request->patient_initials,
                'urgency_level' => $request->urgency_level,

                // Pickup
                'pickup_name' => $request->pickup_name,
                'pickup_address' => $request->pickup_address,
                'pickup_city' => $request->pickup_city,
                'pickup_state' => $request->pickup_state,
                'pickup_zip' => $request->pickup_zip,
                'pickup_phone' => $request->pickup_phone,
                'pickup_contact_person' => $request->pickup_contact_person,
                'pickup_latitude' => $request->pickup_latitude,
                'pickup_longitude' => $request->pickup_longitude,
                'pickup_scheduled_time' => $request->scheduled_time_window_start,

                // Delivery
                'delivery_name' => '',
                'delivery_address' => '',
                // 'delivery_city' => $request->delivery_city,
                // 'delivery_state' => $request->delivery_state,
                // 'delivery_zip' => $request->delivery_zip,
                // 'delivery_phone' => $request->delivery_phone,
                // 'delivery_contact_person' => $request->delivery_contact_person,
                // 'delivery_latitude' => $request->delivery_latitude,
                // 'delivery_longitude' => $request->delivery_longitude,
                'delivery_scheduled_time' => $request->scheduled_time_window_end,

                // Time Window
                'scheduled_time_window_start' => $request->scheduled_time_window_start,
                'scheduled_time_window_end' => $request->scheduled_time_window_end,

                // Driver
                'driver_id' => $request->driver_id,
                'dispatched_at' => $request->driver_id ? ($delivery->dispatched_at ?? now()) : null,

                // Vehicle
                'required_vehicle_type' => $request->required_vehicle_type,

                // Chain of Custody
                'requires_pickup_barcode_scan' => $request->requires_pickup_barcode_scan ?? false,
                'requires_pickup_signature' => $request->requires_pickup_signature ?? true,
                'requires_pickup_photo' => $request->requires_pickup_photo ?? false,
                'requires_recepient_id_scan' => $request->requires_recepient_id_scan ?? false,
                'requires_dropoff_signature' => $request->requires_dropoff_signature ?? false,
                'requires_dropoff_barcode_scan' => $request->requires_dropoff_barcode_scan ?? false,
                'requires_dropoff_photo' => $request->requires_dropoff_photo ?? false,

                'container_count' => $request->container_count ?? 1,

                // Other
                'priority' => $request->priority,
                'special_instructions' => $request->special_instructions,
                'notes' => $request->notes,
                'distance_km' => $request->distance_km,
                'estimated_duration_minutes' => $request->estimated_duration_minutes,
                'status' => $status,
            ]);

            // Recreate delivery items
            $delivery->items()->delete();

            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    if(!$item['search_hospital']){
                        $item['hospital_id'] = null;
                    }
                    DeliveryItem::create(array_merge($item, [
                        'delivery_id' => $delivery->id,
                    ]));
                }
            }

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'delivery_updated',
                'model_type' => Delivery::class,
                'model_id' => $delivery->id,
                'description' => "Updated delivery {$delivery->delivery_number}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Notify only if driver changed
            if ($request->driver_id && $oldDriverId != $request->driver_id) {

                $driver = User::find($request->driver_id);

                if ($driver && $driver->device_token) {

                    SendFirebaseNotificationJob::dispatch(
                        $driver->device_token,
                        'New Job Assigned',
                        'A delivery has been assigned to you.',
                        'mobile',
                        $driver->id,
                        [
                            'type' => 'general',
                            'user_id' => (string) $driver->id,
                            'delivery_id' => (string) $delivery->id,
                        ]
                    );
                }
            }

            DB::commit();

            return $this->successResponse(
                new DeliveryResource($delivery->fresh()->load(['items', 'creator'])),
                'Delivery updated successfully'
            );

        } catch (\Exception $e) {

            DB::rollBack();

            return $this->errorResponse(
                'Failed to update delivery: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * @OA\Get(
     *     path="/deliveries/{id}",
     *     summary="Get delivery details",
     *     tags={"Deliveries"},
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
     *         description="Delivery details retrieved successfully"
     *     ),
     *     @OA\Response(response=404, description="Delivery not found")
     * )
     */
    public function show($id)
    {
        //$query = Delivery::with(['driver', 'creator', 'items.specimenType','items.tempratureRequirement','items.hospital', 'verifications','vehicleRequirement']);
        
        $delivery = Delivery::with([
            'driver',
            'creator',
            'items.specimenType',
    'items.tempratureRequirement',
    'items.hospital',
            'verifications',
            'vehicleRequirement'
        ])->find($id);
        if (!$delivery) {
            return $this->errorResponse('Delivery not found', 200);
        }

        return $this->successResponse(
            new DeliveryResource($delivery),
            'Delivery retrieved successfully'
        );
    }

    /**
     * @OA\Put(
     *     path="/deliveries/{id}",
     *     summary="Update delivery",
     *     tags={"Deliveries"},
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
     *             @OA\Property(property="pickup_name", type="string"),
     *             @OA\Property(property="pickup_address", type="string"),
     *             @OA\Property(property="priority", type="string", enum={"low","normal","high","urgent"}),
     *             @OA\Property(property="special_instructions", type="string"),
     *             @OA\Property(property="notes", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Delivery updated successfully"
     *     ),
     *     @OA\Response(response=404, description="Delivery not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, $id)
    {
        $delivery = Delivery::find($id);

        if (!$delivery) {
            return $this->errorResponse('Delivery not found', 200);
        }

        // Only allow updates if delivery is pending or assigned
        if (!in_array($delivery->status, ['pending', 'assigned'])) {
            return $this->errorResponse('Cannot update delivery in current status', 200);
        }

        $validator = Validator::make($request->all(), [
            'pickup_name' => 'sometimes|string|max:255',
            'pickup_address' => 'sometimes|string',
            'pickup_city' => 'sometimes|string|max:100',
            'pickup_state' => 'sometimes|string|max:50',
            'pickup_zip' => 'sometimes|string|max:20',
            'pickup_phone' => 'sometimes|string|max:20',
            'pickup_contact_person' => 'nullable|string|max:255',
            'pickup_scheduled_time' => 'sometimes|date',

            'delivery_name' => 'sometimes|string|max:255',
            'delivery_address' => 'sometimes|string',
            'delivery_city' => 'sometimes|string|max:100',
            'delivery_state' => 'sometimes|string|max:50',
            'delivery_zip' => 'sometimes|string|max:20',
            'delivery_phone' => 'sometimes|string|max:20',
            'delivery_contact_person' => 'nullable|string|max:255',
            'delivery_scheduled_time' => 'sometimes|date',

            'priority' => 'sometimes|in:low,normal,high,urgent',
            'special_instructions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $delivery->update($request->only([
            'pickup_name', 'pickup_address', 'pickup_city', 'pickup_state', 'pickup_zip',
            'pickup_phone', 'pickup_contact_person', 'pickup_scheduled_time',
            'delivery_name', 'delivery_address', 'delivery_city', 'delivery_state', 'delivery_zip',
            'delivery_phone', 'delivery_contact_person', 'delivery_scheduled_time',
            'priority', 'special_instructions', 'notes'
        ]));

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delivery_updated',
            'model_type' => Delivery::class,
            'model_id' => $delivery->id,
            'description' => "Updated delivery {$delivery->delivery_number}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $this->successResponse(
            new DeliveryResource($delivery->load(['items', 'driver', 'creator'])),
            'Delivery updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/deliveries/{id}",
     *     summary="Delete delivery",
     *     tags={"Deliveries"},
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
     *         description="Delivery deleted successfully"
     *     ),
     *     @OA\Response(response=404, description="Delivery not found")
     * )
     */
    public function destroy(Request $request, $id)
    {
        $delivery = Delivery::find($id);

        if (!$delivery) {
            return $this->errorResponse('Delivery not found', 200);
        }

        // Only allow deletion if delivery is pending
        if ($delivery->status !== 'pending') {
            return $this->errorResponse('Cannot delete delivery in current status', 200);
        }

        // Log activity before deletion
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delivery_deleted',
            'model_type' => Delivery::class,
            'model_id' => $delivery->id,
            'description' => "Deleted delivery {$delivery->delivery_number}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $delivery->delete();

        return $this->successResponse(null, 'Delivery deleted successfully');
    }

    /**
     * @OA\Post(
     *     path="/deliveries/{id}/assign",
     *     summary="Assign driver to delivery",
     *     tags={"Deliveries"},
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
     *             required={"driver_id"},
     *             @OA\Property(property="driver_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Driver assigned successfully"
     *     )
     * )
     */
    public function assignDriver(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $delivery = Delivery::find($id);

        if (!$delivery) {
            return $this->errorResponse('Delivery not found', 200);
        }

        // Only allow assignment for pending or assigned deliveries (for re-assignment)
        if (!in_array($delivery->status, ['pending', 'assigned'])) {
            return $this->errorResponse('Cannot assign driver to delivery in current status', 400);
        }

        $delivery->update([
            'driver_id' => $request->driver_id,
            'status' => 'assigned',
            'dispatched_at' => now(),
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delivery_assigned',
            'model_type' => Delivery::class,
            'model_id' => $delivery->id,
            'description' => "Assigned delivery {$delivery->delivery_number} to driver ID {$request->driver_id}",
            'properties' => ['driver_id' => $request->driver_id],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $this->successResponse(
            new DeliveryResource($delivery->load(['driver', 'items'])),
            'Driver assigned successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/deliveries/{id}/start",
     *     summary="Start delivery (driver picks up items)",
     *     tags={"Deliveries"},
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
     *             @OA\Property(property="latitude", type="number", example=40.7128),
     *             @OA\Property(property="longitude", type="number", example=-74.0060)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Delivery started successfully"
     *     )
     * )
     */
    public function startDelivery(Request $request, $id)
    {
        $delivery = Delivery::find($id);

        if (!$delivery) {
            return $this->errorResponse('Delivery not found', 200);
        }

        if ($delivery->driver_id !== $request->user()->id) {
            return $this->errorResponse('You are not assigned to this delivery', 403);
        }

        if ($delivery->status !== 'assigned') {
            return $this->errorResponse('Delivery cannot be started in current status', 400);
        }

        $delivery->update([
            'status' => 'in_transit',
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delivery_started',
            'model_type' => Delivery::class,
            'model_id' => $delivery->id,
            'description' => "Started delivery {$delivery->delivery_number}",
            'properties' => [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $this->successResponse(
            new DeliveryResource($delivery->load(['items'])),
            'Delivery started successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/deliveries/{id}/pickup",
     *     summary="Confirm pickup with verification",
     *     tags={"Deliveries"},
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
     *             required={"recipient_name"},
     *             @OA\Property(property="recipient_name", type="string", example="John Doe"),
     *             @OA\Property(property="signature_image", type="string", example="base64_encoded_image"),
     *             @OA\Property(property="photo_proof", type="string", example="base64_encoded_image"),
     *             @OA\Property(property="latitude", type="number", example=40.7128),
     *             @OA\Property(property="longitude", type="number", example=-74.0060),
     *             @OA\Property(property="notes", type="string", example="All items collected")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pickup confirmed successfully"
     *     )
     * )
     */
    public function confirmPickup(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'recipient_name' => 'required|string|max:255',
            'signature_image' => 'nullable|string',
            'photo_proof' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $delivery = Delivery::find($id);

        if (!$delivery) {
            return $this->errorResponse('Delivery not found', 200);
        }

        if ($delivery->driver_id !== $request->user()->id) {
            return $this->errorResponse('You are not assigned to this delivery', 403);
        }

        DB::beginTransaction();
        try {
            // Create pickup verification
            DeliveryVerification::create([
                'delivery_id' => $delivery->id,
                'verification_type' => 'pickup',
                'recipient_name' => $request->recipient_name,
                'signature_image' => $request->signature_image,
                'photo_proof' => $request->photo_proof,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'notes' => $request->notes,
                'verified_at' => now(),
            ]);

            // Update delivery status and pickup time
            $delivery->update([
                'status' => 'picked_up',
                'pickup_actual_time' => now(),
            ]);

            // Update all items to collected status
            $delivery->items()->update(['status' => 'collected']);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'delivery_pickup_confirmed',
                'model_type' => Delivery::class,
                'model_id' => $delivery->id,
                'description' => "Confirmed pickup for delivery {$delivery->delivery_number}",
                'properties' => [
                    'recipient_name' => $request->recipient_name,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return $this->successResponse(
                new DeliveryResource($delivery->load(['items', 'verifications'])),
                'Pickup confirmed successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to confirm pickup: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/deliveries/{id}/complete",
     *     summary="Complete delivery with verification",
     *     tags={"Deliveries"},
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
     *             required={"recipient_name"},
     *             @OA\Property(property="recipient_name", type="string", example="Jane Smith"),
     *             @OA\Property(property="signature_image", type="string", example="base64_encoded_image"),
     *             @OA\Property(property="photo_proof", type="string", example="base64_encoded_image"),
     *             @OA\Property(property="latitude", type="number", example=40.7589),
     *             @OA\Property(property="longitude", type="number", example=-73.9851),
     *             @OA\Property(property="notes", type="string", example="Delivered successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Delivery completed successfully"
     *     )
     * )
     */
    public function completeDelivery(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'recipient_name' => 'required|string|max:255',
            'signature_image' => 'nullable|string',
            'photo_proof' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $delivery = Delivery::find($id);

        if (!$delivery) {
            return $this->errorResponse('Delivery not found', 200);
        }

        if ($delivery->driver_id !== $request->user()->id) {
            return $this->errorResponse('You are not assigned to this delivery', 403);
        }

        if ($delivery->status !== 'picked_up') {
            return $this->errorResponse('Cannot complete delivery in current status', 400);
        }

        DB::beginTransaction();
        try {
            // Create delivery verification
            DeliveryVerification::create([
                'delivery_id' => $delivery->id,
                'verification_type' => 'delivery',
                'recipient_name' => $request->recipient_name,
                'signature_image' => $request->signature_image,
                'photo_proof' => $request->photo_proof,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'notes' => $request->notes,
                'verified_at' => now(),
            ]);

            // Update delivery status and delivery time
            $delivery->update([
                'status' => 'delivered',
                'delivery_actual_time' => now(),
            ]);

            // Update all items to delivered status
            $delivery->items()->update(['status' => 'delivered']);

            // Log activity
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'delivery_completed',
                'model_type' => Delivery::class,
                'model_id' => $delivery->id,
                'description' => "Completed delivery {$delivery->delivery_number}",
                'properties' => [
                    'recipient_name' => $request->recipient_name,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return $this->successResponse(
                new DeliveryResource($delivery->load(['items', 'verifications'])),
                'Delivery completed successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to complete delivery: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/deliveries/{id}/cancel",
     *     summary="Cancel delivery",
     *     tags={"Deliveries"},
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
     *             required={"reason"},
     *             @OA\Property(property="reason", type="string", example="Customer request")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Delivery cancelled successfully"
     *     )
     * )
     */
    public function cancelDelivery(Request $request, $id)
    {
        // $validator = Validator::make($request->all(), [
        //     'reason' => 'required|string',
        // ]);

        // if ($validator->fails()) {
        //     return $this->errorResponse('Validation error', 422, $validator->errors());
        // }

        $delivery = Delivery::find($id);

        if (!$delivery) {
            return $this->errorResponse('Delivery not found', 200);
        }

        if ($delivery->status === 'delivered') {
            return $this->errorResponse('Cannot cancel delivered delivery', 400);
        }

        $delivery->update([
            'status' => 'cancelled',
            'notes' => ($delivery->notes ? $delivery->notes . "\n\n" : '') . 'Cancellation reason: ' . $request->reason,
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delivery_cancelled',
            'model_type' => Delivery::class,
            'model_id' => $delivery->id,
            'description' => "Cancelled delivery {$delivery->delivery_number}",
            'properties' => ['reason' => $request->reason],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $this->successResponse(
            new DeliveryResource($delivery),
            'Delivery cancelled successfully'
        );
    }

    public function resumeDelivery(Request $request, $id)
    {
        // $validator = Validator::make($request->all(), [
        //     'reason' => 'required|string',
        // ]);

        // if ($validator->fails()) {
        //     return $this->errorResponse('Validation error', 422, $validator->errors());
        // }

        $delivery = Delivery::find($id);

        if (!$delivery) {
            return $this->errorResponse('Delivery not found', 200);
        }

        if ($delivery->status === 'delivered') {
            return $this->errorResponse('Cannot cancel delivered delivery', 400);
        }
        if ($delivery->driver_id == null) {
            return $this->errorResponse('First assign driver', 400);
        }

        $delivery->update([
            'status' => 'assigned',
            'notes' => ($delivery->notes ? $delivery->notes . "\n\n" : '') . 'Cancellation reason: ' . $request->reason,
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delivery_resumed',
            'model_type' => Delivery::class,
            'model_id' => $delivery->id,
            'description' => "Resumed delivery {$delivery->delivery_number}",
            'properties' => ['reason' => $request->reason],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $this->successResponse(
            new DeliveryResource($delivery),
            'Delivery resumed successfully'
        );
    }

    

    /**
     * @OA\Get(
     *     path="/deliveries/{id}/tracking",
     *     summary="Get delivery tracking information",
     *     tags={"Deliveries"},
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
     *         description="Tracking information retrieved successfully"
     *     )
     * )
     */
    public function tracking($id)
    {
        $delivery = Delivery::with(['driver.driverProfile', 'items', 'verifications'])->find($id);

        if (!$delivery) {
            return $this->errorResponse('Delivery not found', 200);
        }

        $trackingData = [
            'delivery' => new DeliveryResource($delivery),
            'driver_location' => null,
            'timeline' => [],
        ];

        // Add driver current location if available
        if ($delivery->driver && $delivery->driver->driverProfile) {
            $profile = $delivery->driver->driverProfile;
            if ($profile->current_latitude && $profile->current_longitude) {
                $trackingData['driver_location'] = [
                    'latitude' => $profile->current_latitude,
                    'longitude' => $profile->current_longitude,
                    'updated_at' => $profile->updated_at->toIso8601String(),
                ];
            }
        }

        // Build timeline
        $timeline = [];
        $timeline[] = [
            'status' => 'created',
            'timestamp' => $delivery->created_at->toIso8601String(),
            'description' => 'Delivery created',
        ];

        if ($delivery->status !== 'pending') {
            $timeline[] = [
                'status' => 'assigned',
                'timestamp' => $delivery->updated_at->toIso8601String(),
                'description' => 'Driver assigned',
            ];
        }

        if ($delivery->pickup_actual_time) {
            $timeline[] = [
                'status' => 'picked_up',
                'timestamp' => $delivery->pickup_actual_time->toIso8601String(),
                'description' => 'Items picked up',
            ];
        }

        if ($delivery->delivery_actual_time) {
            $timeline[] = [
                'status' => 'delivered',
                'timestamp' => $delivery->delivery_actual_time->toIso8601String(),
                'description' => 'Delivery completed',
            ];
        }

        if ($delivery->status === 'cancelled') {
            $timeline[] = [
                'status' => 'cancelled',
                'timestamp' => $delivery->updated_at->toIso8601String(),
                'description' => 'Delivery cancelled',
            ];
        }

        $trackingData['timeline'] = $timeline;

        return $this->successResponse($trackingData, 'Tracking information retrieved successfully');
    }

    /**
     * Get unique locations for dropdowns
     */
    public function getLocations()
    {
        $pickupLocations = Delivery::select(
            'pickup_name',
            'pickup_address',
            'pickup_city',
            'pickup_state',
            'pickup_zip',
            'pickup_latitude',
            'pickup_longitude'
        )
        ->distinct()
        ->whereNotNull('pickup_address')
        ->get()
        ->map(function ($delivery) {
            return [
                'name' => $delivery->pickup_name,
                'full_address' => trim("{$delivery->pickup_address}, {$delivery->pickup_city}, {$delivery->pickup_state} {$delivery->pickup_zip}"),
                'address' => $delivery->pickup_address,
                'city' => $delivery->pickup_city,
                'state' => $delivery->pickup_state,
                'zip' => $delivery->pickup_zip,
                'latitude' => $delivery->pickup_latitude,
                'longitude' => $delivery->pickup_longitude,
            ];
        })
        ->unique('full_address')
        ->values();

        $deliveryLocations = Delivery::select(
            'delivery_name',
            'delivery_address',
            'delivery_city',
            'delivery_state',
            'delivery_zip',
            'delivery_latitude',
            'delivery_longitude'
        )
        ->distinct()
        ->whereNotNull('delivery_address')
        ->get()
        ->map(function ($delivery) {
            return [
                'name' => $delivery->delivery_name,
                'full_address' => trim("{$delivery->delivery_address}, {$delivery->delivery_city}, {$delivery->delivery_state} {$delivery->delivery_zip}"),
                'address' => $delivery->delivery_address,
                'city' => $delivery->delivery_city,
                'state' => $delivery->delivery_state,
                'zip' => $delivery->delivery_zip,
                'latitude' => $delivery->delivery_latitude,
                'longitude' => $delivery->delivery_longitude,
            ];
        })
        ->unique('full_address')
        ->values();

        return $this->successResponse([
            'pickup_locations' => $pickupLocations,
            'delivery_locations' => $deliveryLocations,
        ], 'Locations retrieved successfully');
    }

    public function monthlyDelivery(){
        $deliveries = Delivery::selectRaw('MONTH(created_at) as month, COUNT(*) as id')
        ->where('created_by', auth()->id())
        ->where('status','delivered')
        ->groupBy('month')
        ->orderBy('month')
        ->get();
        // $deliveries = $query->get();

        $totalDeliveries = Delivery::where('created_by', auth()->id())->count();

        $months = [
        'Jan' => 0, 'Feb' => 0, 'Mar' => 0, 'Apr' => 0,
        'May' => 0, 'Jun' => 0, 'Jul' => 0, 'Aug' => 0,
        'Sep' => 0, 'Oct' => 0, 'Nov' => 0, 'Dec' => 0,
        ];
        // echo '<pre>'; print_r($deliveries);   die;     
        foreach ($deliveries as $delivery) {
            $performance = 0;
            if($delivery->id > 0){
                $performance = $totalDeliveries > 0 
                ? round(($delivery->id / $totalDeliveries) * 100, 2) : 0;
            }
            $monthName = date('M', mktime(0, 0, 0, $delivery->month, 1));
            $months[$monthName] = $performance;
        }

        return $this->successResponse([
            'monthly_deliveries' => $months,
        ], 'Monthly deliveries retrieved successfully');
    }

    function addSpecimenType(StoreSpecimenTempVehicleRequest $request){
        $data = SpecimenType::create($request->validated());
        $data->refresh();
        return $this->successResponse($data, 'Specimen type created successfully');
    }

    function addTemperatureRequirement(StoreSpecimenTempVehicleRequest $request){
        $data = TemperatureRequirement::create($request->validated());
        $data->refresh();
        return $this->successResponse($data, 'Temperature requirement created successfully');
    }
    function addVehicleRequirement(StoreSpecimenTempVehicleRequest $request){
        $data = VehicleRequirement::create($request->validated());
        $data->refresh();
        return $this->successResponse($data, 'Vehicle requirement created successfully');
    }
}
