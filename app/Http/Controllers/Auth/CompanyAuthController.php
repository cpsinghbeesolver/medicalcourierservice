<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Tenant;
use App\Mail\CompanySignupMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CompanyAuthController extends Controller
{
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
        if ($user->role_id != '2') {
            return redirect()->back()->with('error', 'User not allowed to login here');
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error', 'Invalid email or password');
        }

        if ($user->status !== 'active') {
            return redirect()->back()->with('error', 'Your account is not active');
        }

        // Check if email is verified
        if (!$user->email_verified_at) {
            return redirect()->back()->with('error', 'Please verify your email address');
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
            
            // Delete all previous sessions for this user
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', '!=', session()->getId())
                ->delete();
            session(['web_token' => $token]);
            return redirect()->route('company-dashboard');
        }else{
            return redirect()->back()->with('error', 'Invalid email or password');
            // return $this->errorResponse('Invalid email or password', 401);
        }
    }

    public function signup(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'company_url' => 'required|string|max:255|unique:tenants,subdomain',
            //'mobile_no' => 'required|numeric|max:20',
            'mobile_no' => 'required|numeric',
            'email' => 'required|email|unique:users,email',
            // 'message' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->mobile_no,
            'email' => $request->email,
            'email_verified_at' => now(), // Set email as verified immediately for company users
            'role_id' => 2, // Set role_id to 2 for company users
            'password' => Hash::make($request->password),
        ]);
        if($user){
            //Add company name in tenants table
            $tenant = Tenant::create([
                'name' => $request->company_name,
                'subdomain' => strtolower($request->company_url),
            ]);
            //Update tenant_id in users table
            $user->update(['tenant_id' => $tenant->id]);
            $user->refresh();
            //send email to user with login credentials
             try {
                // print_r($submission);die;
                Mail::to($user->email)->send(new CompanySignupMail($user));
            } catch (\Exception $e) {
                Log::error('Failed to send company signup email: ' . $e->getMessage());
            }
            
        }
        return redirect()->route('signup')->with('success', 'Signup successful. Please check email for login instructions.');
    }
}
