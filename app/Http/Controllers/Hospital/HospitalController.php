<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Hospital;
use App\Models\DeliveryItem;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\HospitalCreated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HospitalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $hospital = $user->hospital;
        $items = $hospital->items;
        $drivers = [];
        // dd($items);
        if($items){
            $i = 0;
            foreach ($items as $item) {
                $delivery = $item->delivery;
                //check if driver id exists
                if (!collect($drivers)->contains('id', $delivery->driver_id)) {
                    $driver_profile = \App\Models\DriverProfile::with('user')
                    ->where('user_id', $delivery->driver_id)
                    ->distinct('user_id')
                    ->get()
                    ->map(function ($driver) {
                        return collect([
                            'id' => $driver->user->id,
                            'title' => $driver->user->name,
                            'company_id' => $driver->created_by,
                            'current_latitude' => $driver->current_latitude,
                            'current_longitude' => $driver->current_longitude,
                            'availability_status' => $driver->availability_status
                        ]);
                    }); 
                    // dd($driver_profile[0]);
                    $drivers[] = $driver_profile[0];
                }
                $i++;
            }
        }
        // dd($hospital);
        return view('hospital.maps',compact('hospital','items','drivers'));
    }

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
        if ($user->role_id != '3') {
            return redirect()->back()->with('error', 'User not allowed to login here');
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error', 'Invalid email or password');
        }

        // if ($user->status !== 'active') {
        //     return redirect()->back()->with('error', 'Your account is not active');
        // }

        // Check if email is verified
        // if (!$user->email_verified_at) {
        //     return redirect()->back()->with('error', 'Please verify your email address');
        // }

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            $user->update(['last_login_at' => now()]);
            $deviceName = 'web_app';
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
            return redirect()->route('hospital-dashboard');
        }else{
            return redirect()->back()->with('error', 'Invalid email or password');
            // return $this->errorResponse('Invalid email or password', 401);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hospital_name' => 'required|string|max:255',
            'hospital_registration' => 'required|string|max:255',
            'hospital_email' => 'required|email|unique:users,email|max:255',
            'hospital_phone' => 'required|string|max:30',

            'hospital_address' => 'required|string',
            'hospital_city' => 'nullable|string|max:100',
            'hospital_state' => 'nullable|string|max:100',
            'hospital_zip' => 'nullable|string|max:20',
            'hospital_country' => 'nullable|string|max:100',

            'hospital_lat' => 'nullable|numeric|between:-90,90',
            'hospital_long' => 'nullable|numeric|between:-180,180',

            'hospital_contact_person' => 'required|string|max:255',
        ]);

        $phoneExists = User::whereNotNull('phone')
            ->get(['id', 'phone'])
            ->contains(function ($user) use ($request) {
                return $user->phone === $request->phone;
            });

        if ($phoneExists) {
            return back()
                ->withErrors(['phone' => 'The phone number has already been taken.'])
                ->withInput();
        }

        // Generate a random password
        $password = str_shuffle(
            Str::random(11) . rand(0, 9)
        );
        $user = User::create([
            'name' => $request->hospital_name,
            'email' => $request->hospital_email,
            'password' => bcrypt($password),
            'role_id' => '3',  //For hospital
            'status' => 'active',
        ]);
        
        $hospital = Hospital::create([
            'name' => $validated['hospital_name'],
            'registration_number' => $validated['hospital_registration'] ?? null,
            'hospital_id' => $user->id,
            'phone' => $validated['hospital_phone'] ?? null,

            'address' => $validated['hospital_address'],
            'city' => $validated['hospital_city'] ?? null,
            'state' => $validated['hospital_state'] ?? null,
            'zip' => $validated['hospital_zip'] ?? null,
            'country' => $validated['hospital_country'] ?? null,

            'latitude' => $validated['hospital_lat'] ?? null,
            'longitude' => $validated['hospital_long'] ?? null,

            'contact_person' => $validated['hospital_contact_person'] ?? null,

            'created_by' => $request->user()->id,
        ]);

        //send an email to the hospital with their credentials
        try {
            // print_r($submission);die;
            Mail::to($user->email)->send(new HospitalCreated($request->hospital_email,$password,$request->hospital_name));
        } catch (\Exception $e) {
            Log::error('Failed to send waitlist auto-responder: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Hospital created successfully.',
            'data' => $hospital,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $search = $request->input('search');
        if(!$search){
            return response()->json([
                'status' => 'error',
                'message' => 'Query parameter is required',
            ], 400);
        }

        $hospitals = Hospital::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->select('id', 'name')
            ->limit(20)
            ->get();

        return response()->json([
            'data' => $hospitals
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
