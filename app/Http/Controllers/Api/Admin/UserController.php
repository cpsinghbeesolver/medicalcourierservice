<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/users",
     *     summary="Get list of users",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         description="Filter by role",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Users retrieved successfully"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = User::with('driverProfile');

        // Filter by role
        if ($request->has('role_id')) {
            $query->where('role_id', $request->role_id);
            if($request->role_id == '4'){
                $query->whereHas('driverProfile', function ($q) {
                    $q->where('created_by', Auth::id());
                });
            }
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate($request->get('per_page', 15));

        return $this->successResponse([
            'users' => UserResource::collection($users),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ]
        ], 'Users retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     summary="Create new user",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","role"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="role", type="string", enum={"admin","driver","coordinator"}),
     *             @OA\Property(property="status", type="string", enum={"active","inactive","suspended"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully"
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,driver,coordinator',
            'status' => 'sometimes|in:active,inactive,suspended',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'status' => $request->get('status', 'active'),
        ]);

        // Track user creation for subscription
        if ($request->user()->subscription) {
            $request->user()->subscription->incrementUsage('users', 1);
        }

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'user_created',
            'model_type' => User::class,
            'model_id' => $user->id,
            'description' => "Created new user: {$user->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $this->successResponse(
            new UserResource($user->load('driverProfile')),
            'User created successfully',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Get user details",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User retrieved successfully"
     *     ),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function show($id)
    {
        $user = User::with('driverProfile')->find($id);

        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        return $this->successResponse(
            new UserResource($user),
            'User retrieved successfully'
        );
    }

    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     summary="Update user",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="role", type="string", enum={"admin","driver","coordinator"}),
     *             @OA\Property(property="status", type="string", enum={"active","inactive","suspended"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully"
     *     ),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'phone' => 'sometimes|string|max:20',
            'role' => 'sometimes|in:admin,driver,coordinator',
            'status' => 'sometimes|in:active,inactive,suspended',
            'profile_photo' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $user->update($request->only(['name', 'email', 'phone', 'role', 'status', 'profile_photo']));

        // Log activity
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'user_updated',
            'model_type' => User::class,
            'model_id' => $user->id,
            'description' => "Updated user ID {$id}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $this->successResponse(
            new UserResource($user->load('driverProfile')),
            'User updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="Delete user",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully"
     *     ),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function destroy(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        // Log activity before deletion
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'user_deleted',
            'model_type' => User::class,
            'model_id' => $user->id,
            'description' => "Deleted user ID {$id}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $user->delete();

        return $this->successResponse(null, 'User deleted successfully');
    }

    /**
     * @OA\Get(
     *     path="/drivers/available",
     *     summary="Get available drivers with distance calculation",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="pickup_latitude",
     *         in="query",
     *         description="Pickup location latitude",
     *         required=true,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="pickup_longitude",
     *         in="query",
     *         description="Pickup location longitude",
     *         required=true,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="required_vehicle_type",
     *         in="query",
     *         description="Required vehicle type",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Available drivers retrieved successfully"
     *     )
     * )
     */
    public function getAvailableDrivers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pickup_latitude' => 'required|numeric|between:-90,90',
            'pickup_longitude' => 'required|numeric|between:-180,180',
            'required_vehicle_type' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $pickupLat = $request->pickup_latitude;
        $pickupLng = $request->pickup_longitude;
        $requiredVehicleType = $request->required_vehicle_type;

        // Get all drivers who are:
        // 1. Active users with driver role
        // 2. Clocked in
        // 3. Have availability status of 'available'
        $drivers = User::where('role', 'driver')
            ->where('status', 'active')
            ->whereHas('driverProfile', function ($query) {
                $query->where('is_clocked_in', true)
                      ->where('availability_status', 'available');
            })
            ->with('driverProfile')
            ->get();

        // Filter by vehicle type if specified
        if ($requiredVehicleType) {
            $drivers = $drivers->filter(function ($driver) use ($requiredVehicleType) {
                return $driver->driverProfile &&
                       stripos($driver->driverProfile->vehicle_type, $requiredVehicleType) !== false;
            });
        }

        // Calculate distance for each driver using Haversine formula
        $driversWithDistance = $drivers->map(function ($driver) use ($pickupLat, $pickupLng) {
            $profile = $driver->driverProfile;

            if (!$profile || !$profile->current_latitude || !$profile->current_longitude) {
                return null; // Skip drivers without location data
            }

            $driverLat = $profile->current_latitude;
            $driverLng = $profile->current_longitude;

            // Haversine formula to calculate distance in kilometers
            $earthRadius = 6371; // Earth's radius in kilometers

            $latDiff = deg2rad($pickupLat - $driverLat);
            $lngDiff = deg2rad($pickupLng - $driverLng);

            $a = sin($latDiff / 2) * sin($latDiff / 2) +
                 cos(deg2rad($driverLat)) * cos(deg2rad($pickupLat)) *
                 sin($lngDiff / 2) * sin($lngDiff / 2);

            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $distance = $earthRadius * $c;

            // Estimate travel time (assuming average speed of 40 km/h in city)
            $estimatedMinutes = round(($distance / 40) * 60);

            return [
                'id' => $driver->id,
                'name' => $driver->name,
                'email' => $driver->email,
                'phone' => $driver->phone,
                'profile_photo' => $driver->profile_photo,
                'vehicle_type' => $profile->vehicle_type,
                'vehicle_plate_number' => $profile->vehicle_plate_number,
                'current_latitude' => $profile->current_latitude,
                'current_longitude' => $profile->current_longitude,
                'distance_km' => round($distance, 2),
                'estimated_arrival_minutes' => $estimatedMinutes,
                'availability_status' => $profile->availability_status,
                'is_clocked_in' => $profile->is_clocked_in,
                'clocked_in_at' => $profile->clocked_in_at,
            ];
        })
        ->filter() // Remove null entries
        ->sortBy('distance_km') // Sort by nearest first
        ->values(); // Reset array keys

        return $this->successResponse([
            'drivers' => $driversWithDistance,
            'total' => $driversWithDistance->count(),
        ], 'Available drivers retrieved successfully');
    }
}
