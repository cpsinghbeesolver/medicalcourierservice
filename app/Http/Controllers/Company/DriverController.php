<?php
namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Http\Resources\DriverProfileResource;
use App\Models\DriverProfile;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Storage;
use App\Mail\DriverCreated;
use Illuminate\Support\Facades\Mail;

class DriverController extends Controller
{
    // This controller can be used for driver-specific actions in the future
    public function index()
    {
        // This can be used to show driver-specific dashboard or information
    }

    public function registerDriver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'user_id' => 'required|exists:users,id|unique:driver_profiles,user_id',
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
            'hipaa_certification_file' => 'file|mimes:jpg,jpeg,png,gif,webp,pdf|max:5120',
            'background_check_status' => 'nullable|string|max:50',
            'drug_screen_expiry' => 'nullable|date',
            'specimen_handling_certification_date' => 'nullable|date',
            'specimen_handling_confirmed' => 'nullable|boolean',
            'bloodborne_pathogen_training_date' => 'nullable|date',
            'bloodborne_pathogen_file' => 'file|mimes:jpg,jpeg,png,gif,webp,pdf|max:5120',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'availability_status' => 'nullable|in:available,busy,off_duty',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            //'role' => 'required|in:admin,driver,coordinator',
            'phone' => 'required|nullable|string|max:20',
            'country_code' => 'required'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors()->all())->withInput();
            // return $this->errorResponse('Validation error', 422, $validator->errors());
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

        // Check if user is a driver
        // $user = User::find($request->user_id);
        // if (!$user->isDriver()) {
        //     return $this->errorResponse('User must have driver role', 400);
        // }

        // $profile = DriverProfile::create($request->all());
        $profile = DriverProfile::create([
            'user_id' => $user->id,
            'created_by' => Auth::id(),
            'license_number' => $request->license_number,
            'license_expiry_date' => $request->license_expiry_date,
            'license_state' => $request->license_state,
            'vehicle_type' => $request->vehicle_type,
            'vehicle_plate_number' => $request->vehicle_plate_number,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'country_code' => $request->country_code,
            'date_of_birth' => $request->date_of_birth,
            'temperature_requirements' => $request->temperature_requirement,
            'insurance_policy_number' => $request->insurance_policy,
            'insurance_expiry_date' => $request->insurance_expiry,
            'hipaa_certification_date' => $request->hipaa_cert_date,
            //'hipaa_certification_file' => $request->hipaa_certification_file,
            'background_check_status' => $request->background_check_status,
            'drug_screen_expiry' => $request->drug_screen_expiry,
            'specimen_handling_certification_date' => $request->specimen_handling_cert,
            //'specimen_handling_confirmed' => $request->specimen_cert_confirm ?? false,
            'specimen_handling_confirmed' => true,
            'bloodborne_pathogen_training_date' => $request->bloodborne_training_date,
            //'bloodborne_pathogen_file' => $request->bloodborne_pathogen_file,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'availability_status' => $request->availability_status ?? 'off_duty'
        ]);

        if ($request->hasFile('hipaa_file')) {
            $hipaaCertificationPath = $request->file('hipaa_file')->store('hipaa_certification/' . $user->id, 'public');
            $profile->update([
                'hipaa_certification_file' => Storage::url($hipaaCertificationPath),
            ]);
        }

        if ($request->hasFile('bloodborne_file')) {
            $bloodborneFilePath = $request->file('bloodborne_file')->store('bloodborne_pathogen/' . $user->id, 'public');
            $profile->update([
                'bloodborne_pathogen_file' => Storage::url($bloodborneFilePath),
            ]);    
        }
        

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'driver_profile_created',
            'model_type' => DriverProfile::class,
            'model_id' => $profile->id,
            'description' => "Created driver profile for user ID {$user->id}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        Mail::to($request->email)->send(new DriverCreated($request->email, $request->password, Auth::user()->name, $request->name));
        return back()->with('success', 'Driver Profile created successfully!');
        // TODO: Send email with verification code in production
        
        
        // return $this->successResponse([
        //     'user' => new UserResource($user),
        //     'driver_profile' => new DriverProfileResource($profile),
        //     'email' => $user->email,
        //     'code' => $code, // Remove this in production
        //     'message' => 'Please verify your email to complete registration',
        // ], 'User registered successfully. Please verify your email.', 201);
    }

    public function editDriver(Request $request, $id)
    {
        $profile = DriverProfile::findOrFail($id);
        $user = $profile->user;
        // dd($profile, $user);
        return view('company.driver-edit', [
            'profile' => $profile,
            'user' => $user,
            'id' => $id
        ]);
    }

    public function updateDriver(Request $request)
    {
        // dd($request->all());
        $id = $request->input('user_id');
        $profile = DriverProfile::findOrFail($id);
        $user = $profile->user;

        $validator = Validator::make($request->all(), [
            'license_number' => 'required|string|unique:driver_profiles,license_number,' . $profile->id,
            'license_expiry_date' => 'required|date',
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
            'hipaa_certification_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf|max:5120',
            'background_check_status' => 'nullable|string|max:50',
            'drug_screen_expiry' => 'nullable|date',
            'specimen_handling_certification_date' => 'nullable|date',
            'specimen_handling_confirmed' => 'nullable|boolean',
            'bloodborne_pathogen_training_date' => 'nullable|date',
            'bloodborne_pathogen_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf|max:5120',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'availability_status' => 'nullable|in:available,busy,off_duty',

            'name' => 'required|string|max:255',
            //'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'country_code' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Update User
        $user->name = $request->name;
        //$user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Update Driver Profile
        $profile->update([
            'license_number' => $request->license_number,
            'license_expiry_date' => $request->license_expiry_date,
            'license_state' => $request->license_state,
            'vehicle_type' => $request->vehicle_type,
            'vehicle_plate_number' => $request->vehicle_plate_number,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'country_code' => $request->country_code,
            'date_of_birth' => $request->date_of_birth,
            'temperature_requirements' => $request->temperature_requirement,
            'insurance_policy_number' => $request->insurance_policy,
            'insurance_expiry_date' => $request->insurance_expiry,
            'hipaa_certification_date' => $request->hipaa_cert_date,
            'background_check_status' => $request->background_check_status,
            'drug_screen_expiry' => $request->drug_screen_expiry,
            'specimen_handling_certification_date' => $request->specimen_handling_cert,
            'specimen_handling_confirmed' => true,
            'bloodborne_pathogen_training_date' => $request->bloodborne_training_date,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'availability_status' => $request->availability_status ?? 'off_duty',
        ]);

        if ($request->hasFile('hipaa_file')) {
            
            if ($profile->hipaa_certification_file) {
                Storage::disk('public')->delete(
                    str_replace('/storage/', '', $profile->hipaa_certification_file)
                );
            }

            $path = $request->file('hipaa_file')
                ->store('hipaa_certification/' . $user->id, 'public');
            $profile->update([
                'hipaa_certification_file' => Storage::url($path),
            ]);
        }

        if ($request->hasFile('bloodborne_file')) {

            if ($profile->bloodborne_pathogen_file) {
                Storage::disk('public')->delete(
                    str_replace('/storage/', '', $profile->bloodborne_pathogen_file)
                );
            }

            $path = $request->file('bloodborne_file')
                ->store('bloodborne_pathogen/' . $user->id, 'public');

            $profile->update([
                'bloodborne_pathogen_file' => Storage::url($path),
            ]);
        }

        return back()->with('success', 'Driver Profile updated successfully!');
    }
}
