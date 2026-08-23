<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DriverProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\RefreshToken;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MobileAuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/mobile/v1/login",
     *     summary="Mobile user login",
     *     tags={"Mobile - Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="driver@test.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="device_type", type="string", example="iPhone 14 Pro"),
     *             @OA\Property(property="fcm_token", type="string", example="firebase_token_here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="email", type="string"),
     *                     @OA\Property(property="role", type="string"),
     *                     @OA\Property(property="phone", type="string"),
     *                     @OA\Property(property="dob", type="string", format="date", example="1990-05-15", nullable=true),
     *                     @OA\Property(property="address", type="string", example="123 Main Street, New York, NY 10001", nullable=true),
     *                     @OA\Property(property="profile_photo", type="string", nullable=true),
     *                     @OA\Property(property="driver_profile", type="object", nullable=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Invalid email or password"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function login(Request $request)
    {
        //print_r($request->all());die;
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'device_type' => 'nullable|string|max:255',
            'fcm_token' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Account is not active'
            ], 403);
        }

        //Update Device Token
        $user->update(['device_token' => $request->fcm_token,'device_type' => $request->device_type]);
        $user->refresh();
        // Create token
        $deviceName = $request->device_type ?? 'mobile-app';

        // Remove all previous tokens
        $user->tokens()->delete();
        $token = $user->createToken($deviceName)->plainTextToken;

        // Load driver profile if user is a driver
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'phone' => $user->phone,
            'dob' => $user->dob?->format('Y-m-d'),
            'address' => $user->address,
            'profile_photo' => $user->profile_photo,
            'status' => $user->status
        ];

        // $driverProfile = DriverProfile::where('user_id',$user->id)->first();
        // $driverProfile->update([
        //     'iso_code'     => $request->iso_code,
        //     'country_code' => $request->country_code,
        //     'country_flag' => $request->country_flag,
        // ]);
        // $driverProfile->refresh();

        if ($user->role_id == '4') {
            $driverProfile = DriverProfile::where('user_id', $user->id)->first();
            if($driverProfile){
                $driverProfile->refresh(); 
                $userData['iso_code'] = $driverProfile->iso_code;
                $userData['country_code'] = $driverProfile->country_code;
                $userData['country_flag'] = $driverProfile->country_flag;
                $userData['company_id'] = $driverProfile->created_by;
                $userData['driver_profile'] = $driverProfile ? [
                    'id' => $driverProfile->id,
                    'license_number' => $driverProfile->license_number,
                    'vehicle_type' => $driverProfile->vehicle_type,
                    'vehicle_plate_number' => $driverProfile->vehicle_plate_number,
                    'date_of_birth' => $driverProfile->date_of_birth?->format('Y-m-d'),
                    'address' => $driverProfile->address,
                    'availability_status' => $driverProfile->availability_status,
                    'current_location' => [
                        'latitude' => $driverProfile->current_latitude,
                        'longitude' => $driverProfile->current_longitude
                    ],
                    
                ] : null;
            }
        }


        // Refresh token (long-lived)
        $plainRefreshToken = Str::random(64);

        RefreshToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $plainRefreshToken),
            'expires_at' => now()->addDays(7),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
                'refresh_token' => $plainRefreshToken,
                'user' => $userData
            ]
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/mobile/v1/refresh",
     *     summary="Refresh mobile user token",
     *     tags={"Mobile - Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"refresh_token"},
     *             @OA\Property(property="refresh_token", type="string", example="refresh_token_here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful token refresh",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Token refreshed successfully"),
     *             @OA\Property(property="data", type="object",
     *                @OA\Property(property="refresh_token", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Invalid email or password"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        $tokenHash = hash('sha256', $request->refresh_token);

        $validToken = RefreshToken::with('user')
            ->where('token', $tokenHash)
            ->where('revoked', false)
            ->first();

        if (!$validToken || $validToken->expires_at->isPast()) {
            return response()->json([
                'error' => 'Invalid or expired refresh token'
            ], 401);
        }

        $user = $validToken->user;

        $validToken->update([
            'revoked' => true
        ]);

        $accessToken = $user->createToken('api-token')->plainTextToken;

        $newRefreshToken = Str::random(64);

        RefreshToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $newRefreshToken),
            'expires_at' => now()->addDays(7),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data' => [
                'access_token' => $accessToken,
                'refresh_token' => $newRefreshToken,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/refresh-expiry",
     *     summary="Check if refresh token is still valid and extend its expiry",
     *     tags={"Mobile - Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"refresh_token"},
     *             @OA\Property(property="refresh_token", type="string", example="refresh_token_here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful token refresh",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Refresh token is not yet expired"),
     *             @OA\Property(property="data", type="object",
     *                @OA\Property(property="refresh_token", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Invalid email or password"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function refreshExpiry(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string'
        ]);

        $tokenHash = hash('sha256', $request->refresh_token);

        $refreshToken = RefreshToken::where('token', $tokenHash)
            ->where('revoked', false)
            ->first();

        if (!$refreshToken) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid refresh token',
                'expires_at' => null
            ], 401);
        }

        if ($refreshToken->expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Refresh token is expired',
                'expires_at' => $refreshToken->expires_at
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Refresh token is not yet expired',
            'expires_at' => $refreshToken->expires_at
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/logout",
     *     summary="Mobile user logout",
     *     tags={"Mobile - Authentication"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful logout",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        // revoke all refresh tokens of user
        RefreshToken::where('user_id', $request->user()->id)
                    ->update(['revoked' => true]);
        $driverProfile = DriverProfile::where('user_id', $request->user()->id)->first();
        $driverProfile->availability_status = 'off_duty';
        $driverProfile->save();
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/profile",
     *     summary="Get current user profile",
     *     tags={"Mobile - Authentication"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User profile",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
         )
     *     )
     * )
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'phone' => $user->phone,
            'dob' => $user->dob?->format('Y-m-d'),
            'address' => $user->address,
            'profile_photo' => $user->profile_photo,
            'status' => $user->status,
            'created_at' => $user->created_at
        ];

        if ($user->role === 'driver') {
            $driverProfile = DriverProfile::where('user_id', $user->id)->first();
            $userData['driver_profile'] = $driverProfile;
        }

        return response()->json([
            'success' => true,
            'data' => $userData
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/mobile/v1/profile",
     *     summary="Update user profile",
     *     tags={"Mobile - Authentication"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Driver"),
     *             @OA\Property(property="email", type="string", format="email", example="driver@test.com"),
     *             @OA\Property(property="role", type="string", example="driver"),
     *             @OA\Property(property="phone", type="string", example="+1552 4422"),
     *             @OA\Property(property="dob", type="string", format="date", example="1990-05-15"),
     *             @OA\Property(property="address", type="string", example="123 Main Street, New York, NY 10001"),
     *             @OA\Property(property="profile_photo", type="string", example="string"),
     *             @OA\Property(property="status", type="string", example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Profile updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'role' => 'sometimes|string|in:admin,dispatcher,driver,facility_manager',
            'phone' => 'sometimes|string|max:20',
            'dob' => 'sometimes|date',
            'address' => 'sometimes|string',
            'profile_photo' => 'sometimes|string',
            'status' => 'sometimes|string|in:active,inactive,suspended'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only(['name', 'email', 'role', 'phone', 'dob', 'address', 'profile_photo', 'status']));

        // Reload user from database to get decrypted values
        $updatedUser = User::find($user->id);
        $driverProfile = DriverProfile::where('user_id',$updatedUser->id)->first();
        $driverProfile->update([
            'iso_code'     => $request->iso_code,
            'country_code' => $request->country_code,
            'country_flag' => $request->country_flag,
        ]);
        $driverProfile->refresh();
        
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'id' => $updatedUser->id,
                'name' => $updatedUser->name,
                'email' => $updatedUser->email,
                'role' => $updatedUser->role,
                'phone' => $updatedUser->phone,
                
                'address' => $updatedUser->address,
                'profile_photo' => $updatedUser->profile_photo,
                'status' => $updatedUser->status,
                'company_id' => $driverProfile->created_by,
                'iso_code'     => $driverProfile->iso_code,
                'country_code' => $driverProfile->country_code,
                'country_flag' => $driverProfile->country_flag,
                'driver_profile' => $driverProfile ? [
                    'date_of_birth' => $driverProfile->date_of_birth?->format('Y-m-d')
                ] : null
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/change-password",
     *     summary="Change password",
     *     tags={"Mobile - Authentication"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password", "new_password"},
     *             @OA\Property(property="current_password", type="string", format="password"),
     *             @OA\Property(property="new_password", type="string", format="password"),
     *             @OA\Property(property="new_password_confirmation", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Password changed successfully"),
     *     @OA\Response(response=400, description="Current password is incorrect")
     * )
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }
}
