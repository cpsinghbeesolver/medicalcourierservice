<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Models\DriverProfile;
use App\Models\User;
use App\Models\ActivityLog;
use App\Http\Resources\DriverProfileResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class DriverProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/driver-profiles",
     *     summary="Get list of driver profiles",
     *     tags={"Driver Profiles"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="availability_status",
     *         in="query",
     *         description="Filter by availability status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"available","busy","off_duty"})
     *     ),
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         description="Filter by city",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Driver profiles retrieved successfully"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = DriverProfile::with('user')->where('created_by', $request->user()->id);
        // dd($query->get());
        // Filter by availability status
        if ($request->has('availability_status')) {
            $query->where('availability_status', $request->availability_status);
        }

        // Filter by city
        if ($request->has('city')) {
            $query->where('city', 'LIKE', '%' . $request->city . '%');
        }

        // Filter by state
        if ($request->has('state')) {
            $query->where('state', $request->state);
        }

        // Filter by user status
        if ($request->has('status')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // Search by driver name, email, license or vehicle type
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($sub) use ($search) {
                $sub->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                })
                ->orWhere('license_number', 'LIKE', "%{$search}%")
                ->orWhere('vehicle_type', 'LIKE', "%{$search}%");
            });
        }

        // Check for expired licenses
        if ($request->has('license_expired') && $request->license_expired === 'true') {
            $query->where('license_expiry_date', '<', now());
        }
        $query->orderBy('created_at', 'desc');

        $profiles = $query->paginate($request->get('per_page', 15));
        // $profiles = $query->get();
        return $this->successResponse([
            'profiles' => DriverProfileResource::collection($profiles),
            'pagination' => [
                'total' => $profiles->total(),
                'per_page' => $profiles->perPage(),
                'current_page' => $profiles->currentPage(),
                'last_page' => $profiles->lastPage(),
            ]
        ], 'Driver profiles retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/driver-profiles",
     *     summary="Create a new driver profile",
     *     tags={"Driver Profiles"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id","license_number","license_expiry_date"},
     *             @OA\Property(property="user_id", type="integer", example=2),
     *             @OA\Property(property="license_number", type="string", example="DL123456789"),
     *             @OA\Property(property="license_expiry_date", type="string", format="date", example="2027-12-31"),
     *             @OA\Property(property="vehicle_type", type="string", example="Van"),
     *             @OA\Property(property="vehicle_plate_number", type="string", example="ABC-1234"),
     *             @OA\Property(property="address", type="string", example="123 Driver St"),
     *             @OA\Property(property="city", type="string", example="New York"),
     *             @OA\Property(property="state", type="string", example="NY"),
     *             @OA\Property(property="zip_code", type="string", example="10001"),
     *             @OA\Property(property="emergency_contact_name", type="string", example="Jane Doe"),
     *             @OA\Property(property="emergency_contact_phone", type="string", example="+1234567890")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Driver profile created successfully"
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id|unique:driver_profiles,user_id',
            'license_number' => 'required|string|unique:driver_profiles,license_number',
            'license_expiry_date' => 'required|date|after:today',
            'license_state' => 'nullable|string|max:50',
            'vehicle_type' => 'nullable|string|max:100',
            'vehicle_plate_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zip_code' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'insurance_policy_number' => 'nullable|string|max:100',
            'insurance_expiry_date' => 'nullable|date',
            'hipaa_certification_date' => 'nullable|date',
            'hipaa_certification_file' => 'nullable|string|max:255',
            'background_check_status' => 'nullable|string|max:50',
            'drug_screen_expiry' => 'nullable|date',
            'specimen_handling_certification_date' => 'nullable|date',
            'specimen_handling_confirmed' => 'nullable|boolean',
            'bloodborne_pathogen_training_date' => 'nullable|date',
            'bloodborne_pathogen_file' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'availability_status' => 'nullable|in:available,busy,off_duty',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        // Check if user is a driver
        $user = User::find($request->user_id);
        if (!$user->isDriver()) {
            return $this->errorResponse('User must have driver role', 400);
        }

        $profile = DriverProfile::create($request->all());

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'driver_profile_created',
            'model_type' => DriverProfile::class,
            'model_id' => $profile->id,
            'description' => "Created driver profile for user ID {$request->user_id}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $this->successResponse(
            new DriverProfileResource($profile->load('user')),
            'Driver profile created successfully',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/driver-profiles/{id}",
     *     summary="Get driver profile details",
     *     tags={"Driver Profiles"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Driver Profile ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Driver profile retrieved successfully"
     *     ),
     *     @OA\Response(response=404, description="Driver profile not found")
     * )
     */
    public function show($id)
    {
        $profile = DriverProfile::with('user')->find($id);
        if (!$profile) {
            return $this->errorResponse('Driver profile not found', 404);
        }

        return $this->successResponse(
            new DriverProfileResource($profile),
            'Driver profile retrieved successfully'
        );
    }

    /**
     * @OA\Put(
     *     path="/driver-profiles/{id}",
     *     summary="Update driver profile",
     *     tags={"Driver Profiles"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Driver Profile ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="license_number", type="string"),
     *             @OA\Property(property="license_expiry_date", type="string", format="date"),
     *             @OA\Property(property="vehicle_type", type="string"),
     *             @OA\Property(property="vehicle_plate_number", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="city", type="string"),
     *             @OA\Property(property="state", type="string"),
     *             @OA\Property(property="zip_code", type="string"),
     *             @OA\Property(property="emergency_contact_name", type="string"),
     *             @OA\Property(property="emergency_contact_phone", type="string"),
     *             @OA\Property(property="availability_status", type="string", enum={"available","busy","off_duty"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Driver profile updated successfully"
     *     ),
     *     @OA\Response(response=404, description="Driver profile not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, $id)
    {
        $profile = DriverProfile::find($id);

        if (!$profile) {
            return $this->errorResponse('Driver profile not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'license_number' => 'sometimes|string|unique:driver_profiles,license_number,' . $id,
            'license_expiry_date' => 'sometimes|date',
            'license_state' => 'nullable|string|max:50',
            'vehicle_type' => 'nullable|string|max:100',
            'vehicle_plate_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zip_code' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'insurance_policy_number' => 'nullable|string|max:100',
            'insurance_expiry_date' => 'nullable|date',
            'hipaa_certification_date' => 'nullable|date',
            'hipaa_certification_file' => 'nullable|string|max:255',
            'background_check_status' => 'nullable|string|max:50',
            'drug_screen_expiry' => 'nullable|date',
            'specimen_handling_certification_date' => 'nullable|date',
            'specimen_handling_confirmed' => 'nullable|boolean',
            'bloodborne_pathogen_training_date' => 'nullable|date',
            'bloodborne_pathogen_file' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'availability_status' => 'nullable|in:available,busy,off_duty',
            'current_latitude' => 'nullable|numeric|between:-90,90',
            'current_longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $profile->update($request->all());

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'driver_profile_updated',
            'model_type' => DriverProfile::class,
            'model_id' => $profile->id,
            'description' => "Updated driver profile ID {$id}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $this->successResponse(
            new DriverProfileResource($profile->load('user')),
            'Driver profile updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/driver-profiles/{id}",
     *     summary="Delete driver profile",
     *     tags={"Driver Profiles"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Driver Profile ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Driver profile deleted successfully"
     *     ),
     *     @OA\Response(response=404, description="Driver profile not found")
     * )
     */
    public function destroy(Request $request, $id)
    {
        $profile = DriverProfile::find($id);

        if (!$profile) {
            return $this->errorResponse('Driver profile not found', 404);
        }

        // Log activity before deletion
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'driver_profile_deleted',
            'model_type' => DriverProfile::class,
            'model_id' => $profile->id,
            'description' => "Deleted driver profile ID {$id}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $profile->delete();

        return $this->successResponse(null, 'Driver profile deleted successfully');
    }
}
