<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","role"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
     *             @OA\Property(property="role", type="string", enum={"admin","driver","dispatcher"}, example="driver"),
     *             @OA\Property(property="phone", type="string", example="+1234567890")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com")
     *                 ),
     *                 @OA\Property(property="token", type="string", example="1|abc123...")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function register(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,driver,coordinator',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors()->all());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => '4',
            'phone' => $request->phone,
            'status' => 'active',
        ]);

        // Generate verification code
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'verification_code' => $code,
            'verification_code_expires_at' => now()->addMinutes(15),
        ]);

        // TODO: Send email with verification code in production

        return $this->successResponse([
            'user' => new UserResource($user),
            'email' => $user->email,
            'code' => $code, // Remove this in production
            'message' => 'Please verify your email to complete registration',
        ], 'User registered successfully. Please verify your email.', 201);
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Login user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="device_name", type="string", example="mobile_app")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com")
     *                 ),
     *                 @OA\Property(property="token", type="string", example="1|abc123...")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Invalid email or password")
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
            //return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $user = User::where('email', $request->email)->first();
        if(!$user){
            return redirect()->back()->with('error', 'Invalid email or password');
        }
        if($user->role_id !== 1){
            return redirect()->back()->with('error', 'User not allowed to login here');
        }
        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error', 'Invalid email or password');
            // return $this->errorResponse('Invalid email or password', 401);
        }

        if ($user->status !== 'active') {
            return redirect()->back()->with('error', 'Your account is not active');
            // return $this->errorResponse('Your account is not active', 403);
        }

        // Check if email is verified
        if (!$user->email_verified_at) {
            // Generate and send verification code
            // $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            // $user->update([
            //     'verification_code' => $code,
            //     'verification_code_expires_at' => now()->addMinutes(15),
            // ]);

            // TODO: Send email with verification code in production
            return redirect()->back()->with('error', 'Please verify your email address');
            // return $this->successResponse([
            //     'email_verified' => false,
            //     'email' => $user->email,
            //     'code' => $code, // Remove this in production
            //     'message' => 'Please verify your email address',
            // ], 'Email verification required', 200);
        }

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            $user->update(['last_login_at' => now()]);
            $deviceName = $request->device_name ?? 'mobile_app';
            $token = $user->createToken($deviceName)->plainTextToken;
            
            // Login user
            //Auth::login($user);
            $request->session()->regenerate();
            session(['web_token' => $token]);
            return redirect()->route('dashboard');
        }else{
            return redirect()->back()->with('error', 'Invalid email or password');
            // return $this->errorResponse('Invalid email or password', 401);
        }
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     summary="Logout user",
     *     tags={"Authentication"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logged out successfully"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        
        if(auth()->user()->isAdmin()){
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/admin');
        }
        else if(auth()->user()->isHospital()){
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/hospital/login');
        }
        else{
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/company/login');
        }
        // return $this->successResponse(null, 'Logged out successfully');
    }

    /**
     * @OA\Get(
     *     path="/me",
     *     summary="Get current user profile",
     *     tags={"Authentication"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User profile retrieved successfully"
     *     )
     * )
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('driverProfile')->load('hospital');
        return $this->successResponse(
            new UserResource($user),
            'Profile retrieved successfully'
        );
    }

    /**
     * @OA\Put(
     *     path="/profile",
     *     summary="Update user profile",
     *     tags={"Authentication"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="phone", type="string", example="+1234567890"),
     *             @OA\Property(property="profile_photo", type="string", example="base64_image_string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully"
     *     )
     * )
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'dob' => 'sometimes|nullable|date|before:today',
            'address' => 'sometimes|nullable|string|max:500',
            'profile_photo' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $updateData = [];

        // Update name
        if ($request->has('name')) {
            $updateData['name'] = $request->name;
        }

        // Update phone
        if ($request->has('phone')) {
            $updateData['phone'] = $request->phone;
        }

        // Update date of birth
        if ($request->has('dob')) {
            $updateData['dob'] = $request->dob;
        }

        // Update address
        if ($request->has('address')) {
            $updateData['address'] = $request->address;
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo && \Storage::disk('public')->exists($user->profile_photo)) {
                \Storage::disk('public')->delete($user->profile_photo);
            }

            // Store new photo
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $updateData['profile_photo'] = $path;
        }

        // Update user
        $user->update($updateData);

        // Log the activity
        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'profile_updated',
            'description' => 'User updated their profile',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Refresh the user model to get the latest data
        $user->refresh();

        return $this->successResponse(
            new UserResource($user),
            'Profile updated successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/change-password",
     *     summary="Change user password",
     *     tags={"Authentication"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password","new_password"},
     *             @OA\Property(property="current_password", type="string", format="password"),
     *             @OA\Property(property="new_password", type="string", format="password"),
     *             @OA\Property(property="new_password_confirmation", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password changed successfully"
     *     )
     * )
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed|different:current_password',
        ], [
            'new_password.different' => 'New password must be different from current password.',
            'new_password.min' => 'New password must be at least 8 characters.',
            'new_password.confirmed' => 'New password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $user = $request->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return $this->errorResponse('Current password is incorrect', 400);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Revoke all tokens except current one (optional - for security)
        // $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        // Log the activity
        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'password_changed',
            'description' => 'User changed their password',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Send email notification about password change
        try {
            \Mail::to($user->email)->send(new \App\Mail\PasswordChangedNotification($user));
        } catch (\Exception $e) {
            \Log::error('Failed to send password change notification: ' . $e->getMessage());
        }

        return $this->successResponse(null, 'Password changed successfully. Please use your new password for future logins.');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/forgot-password",
     *     summary="Send password reset link via email",
     *     description="Sends a secure password reset link to the user's email address. The link expires in 60 minutes.",
     *     operationId="forgotPassword",
     *     tags={"Password Reset"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User email address",
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 example="driver@test.com",
     *                 description="Registered email address"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset link sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password reset link sent to your email"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="If your email exists in our system, you will receive a password reset link shortly."),
     *                 @OA\Property(property="dev_token", type="string", nullable=true, example="a1b2c3d4e5f6...", description="Reset token (only in development)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email field is required."))
     *             )
     *         )
     *     )
     * )
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $user = User::where('email', $request->email)->first();

        // Generate secure token
        $token = bin2hex(random_bytes(32));
        $expiresAt = now()->addHour();

        // Store reset token in database
        \DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'type' => 'link',
            'expires_at' => $expiresAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create reset URL
        $resetUrl = config('app.url') . '/reset-password?token=' . $token . '&email=' . urlencode($request->email);

        // Send email
        try {
            \Mail::to($user->email)->send(new \App\Mail\PasswordResetLinkMail(
                $user->name,
                $resetUrl,
                60 // expires in 60 minutes
            ));
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset email', [
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
        }

        return $this->successResponse([
            'message' => 'If your email exists in our system, you will receive a password reset link shortly.',
            // For development/testing only - remove in production
            'dev_token' => app()->environment('local') ? $token : null,
        ], 'Password reset link sent to your email');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/password/send-otp",
     *     summary="Send OTP code for password reset (Mobile)",
     *     description="Sends a 6-digit OTP code to the user's email for password reset. Designed for mobile apps. OTP expires in 15 minutes.",
     *     operationId="sendOtp",
     *     tags={"Password Reset"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User email address",
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 example="driver@test.com",
     *                 description="Registered email address"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP sent to your email"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="If your email exists in our system, you will receive an OTP code shortly."),
     *                 @OA\Property(property="expires_in_minutes", type="integer", example=15),
     *                 @OA\Property(property="dev_otp", type="string", nullable=true, example="123456", description="OTP code (only in development)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email field is required."))
     *             )
     *         )
     *     )
     * )
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $user = User::where('email', $request->email)->first();

        // Generate 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(15);

        // Store OTP in database
        \DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => Hash::make($otp),
            'otp' => $otp,
            'type' => 'otp',
            'expires_at' => $expiresAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send email with OTP
        try {
            \Mail::to($user->email)->send(new \App\Mail\PasswordResetOtpMail(
                $user->name,
                $otp,
                15 // expires in 15 minutes
            ));
        } catch (\Exception $e) {
            \Log::error('Failed to send OTP email', [
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
        }

        return $this->successResponse([
            'message' => 'If your email exists in our system, you will receive an OTP code shortly.',
            'expires_in_minutes' => 15,
            // For development/testing only - remove in production
            'dev_otp' => app()->environment('local') ? $otp : null,
        ], 'OTP sent to your email');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/password/verify-otp",
     *     summary="Verify OTP code (Optional Step)",
     *     description="Verifies the OTP code before password reset. This is an optional step - you can skip directly to reset-with-otp.",
     *     operationId="verifyOtp",
     *     tags={"Password Reset"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Email and OTP code",
     *         @OA\JsonContent(
     *             required={"email","otp"},
     *             @OA\Property(property="email", type="string", format="email", example="driver@test.com"),
     *             @OA\Property(property="otp", type="string", example="123456", description="6-digit OTP code")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP verified successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="verified", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="OTP verified successfully. You can now reset your password.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid or expired OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid or expired OTP. Please request a new one."),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="otp", type="array", @OA\Items(type="string", example="The otp field must be 6 characters."))
     *             )
     *         )
     *     )
     * )
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        // Find the most recent OTP for this email
        $resetRecord = \DB::table('password_resets')
            ->where('email', $request->email)
            ->where('type', 'otp')
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->latest('created_at')
            ->first();

        if (!$resetRecord) {
            return $this->errorResponse('Invalid or expired OTP. Please request a new one.', 400);
        }

        // Verify OTP
        if ($resetRecord->otp !== $request->otp) {
            return $this->errorResponse('Invalid OTP code', 400);
        }

        return $this->successResponse([
            'verified' => true,
            'message' => 'OTP verified successfully. You can now reset your password.',
        ], 'OTP verified successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/reset-password",
     *     summary="Reset password using token from email link",
     *     description="Resets user password using the token received in the email reset link. Token expires in 60 minutes.",
     *     operationId="resetPassword",
     *     tags={"Password Reset"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Password reset data with token",
     *         @OA\JsonContent(
     *             required={"email","password","password_confirmation","token"},
     *             @OA\Property(property="email", type="string", format="email", example="driver@test.com"),
     *             @OA\Property(property="token", type="string", example="a1b2c3d4e5f6...", description="Reset token from email link"),
     *             @OA\Property(property="password", type="string", format="password", example="NewPassword123", description="New password (min 8 characters)"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="NewPassword123", description="Confirm new password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password reset successfully. You can now login with your new password."),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid or expired token",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid or expired reset token"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="password", type="array", @OA\Items(type="string", example="The password must be at least 8 characters."))
     *             )
     *         )
     *     )
     * )
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        // Find valid reset record
        $resetRecords = \DB::table('password_resets')
            ->where('email', $request->email)
            ->where('type', 'link')
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->get();

        $validRecord = null;
        foreach ($resetRecords as $record) {
            if (Hash::check($request->token, $record->token)) {
                $validRecord = $record;
                break;
            }
        }

        if (!$validRecord) {
            return $this->errorResponse('Invalid or expired reset token', 400);
        }

        // Update user password
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Mark token as used
        \DB::table('password_resets')
            ->where('id', $validRecord->id)
            ->update([
                'used' => true,
                'used_at' => now(),
            ]);

        // Delete all other unused tokens for this email
        \DB::table('password_resets')
            ->where('email', $request->email)
            ->where('used', false)
            ->delete();

        // Log activity
        \App\Services\HipaaAuditLogger::logPhiAccess(
            'password_reset',
            'user',
            $user->id,
            ['email'],
            ['method' => 'reset_link']
        );

        return $this->successResponse(null, 'Password reset successfully. You can now login with your new password.');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/password/reset-with-otp",
     *     summary="Reset password using OTP code (Mobile)",
     *     description="Resets user password using the 6-digit OTP code received via email. Designed for mobile apps. OTP expires in 15 minutes.",
     *     operationId="resetPasswordWithOtp",
     *     tags={"Password Reset"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Password reset data with OTP",
     *         @OA\JsonContent(
     *             required={"email","otp","password","password_confirmation"},
     *             @OA\Property(property="email", type="string", format="email", example="driver@test.com"),
     *             @OA\Property(property="otp", type="string", example="123456", description="6-digit OTP code from email"),
     *             @OA\Property(property="password", type="string", format="password", example="NewPassword123", description="New password (min 8 characters)"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="NewPassword123", description="Confirm new password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password reset successfully. You can now login with your new password."),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid or expired OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid or expired OTP. Please request a new one."),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="password", type="array", @OA\Items(type="string", example="The password must be at least 8 characters.")),
     *                 @OA\Property(property="otp", type="array", @OA\Items(type="string", example="The otp field must be 6 characters."))
     *             )
     *         )
     *     )
     * )
     */
    public function resetPasswordWithOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        // Find the most recent OTP for this email
        $resetRecord = \DB::table('password_resets')
            ->where('email', $request->email)
            ->where('type', 'otp')
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->latest('created_at')
            ->first();

        if (!$resetRecord) {
            return $this->errorResponse('Invalid or expired OTP. Please request a new one.', 400);
        }

        // Verify OTP
        if ($resetRecord->otp !== $request->otp) {
            return $this->errorResponse('Invalid OTP code', 400);
        }

        // Update user password
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Mark OTP as used
        \DB::table('password_resets')
            ->where('id', $resetRecord->id)
            ->update([
                'used' => true,
                'used_at' => now(),
            ]);

        // Delete all other unused OTPs for this email
        \DB::table('password_resets')
            ->where('email', $request->email)
            ->where('used', false)
            ->delete();

        // Log activity
        \App\Services\HipaaAuditLogger::logPhiAccess(
            'password_reset',
            'user',
            $user->id,
            ['email'],
            ['method' => 'otp']
        );

        return $this->successResponse(null, 'Password reset successfully. You can now login with your new password.');
    }

    /**
     * @OA\Post(
     *     path="/send-verification-code",
     *     summary="Send email verification code",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Verification code sent successfully"
     *     )
     * )
     */
    public function sendVerificationCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $user = User::where('email', $request->email)->first();

        // Generate 6-digit code
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Save code and expiry (15 minutes)
        $user->update([
            'verification_code' => $code,
            'verification_code_expires_at' => now()->addMinutes(15),
        ]);

        // TODO: Send email with verification code
        // For now, we'll just return success
        // In production, use Laravel Mail to send the code

        return $this->successResponse([
            'code' => $code, // Remove this in production
            'expires_at' => $user->verification_code_expires_at->toDateTimeString(),
        ], 'Verification code sent to your email');
    }

    /**
     * @OA\Post(
     *     path="/verify-code",
     *     summary="Verify email with code",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","code"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="code", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email verified successfully"
     *     )
     * )
     */
    public function verifyCode(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $user = User::where('email', $request->email)->first();

        // Check if code exists
        if (!$user->verification_code) {
            return $this->errorResponse('No verification code found. Please request a new one.', 400);
        }

        // Check if code expired
        if ($user->verification_code_expires_at < now()) {
            return $this->errorResponse('Verification code has expired. Please request a new one.', 400);
        }

        // Check if code matches
        if ($user->verification_code !== $request->code) {
            return $this->errorResponse('Invalid verification code', 400);
        }

        // Mark email as verified and clear verification code
        $user->update([
            'email_verified_at' => now(),
            'verification_code' => null,
            'verification_code_expires_at' => null,
        ]);

        // Generate token for auto-login after verification
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Email verified successfully');
    }


    public function submitVerify(Request $request){
        $request->validate([
            'email' => 'required|email',
        ]);

        // Find user
        $user = User::where('email', $request->email)->first();
        // dd($user);
        // User not found
        if (!$user) {
            return back()->withErrors([
                'email' => 'User not found',
            ]);
        }

        // Login user
        Auth::login($user);
        // Regenerate session
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * @OA\Post(
     *     path="/resend-verification-code",
     *     summary="Resend verification code",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Verification code resent successfully"
     *     )
     * )
     */
    public function resendVerificationCode(Request $request)
    {
        return $this->sendVerificationCode($request);
    }
}
