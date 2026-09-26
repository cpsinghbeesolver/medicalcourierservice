<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class HospitalController extends Controller
{
    /**
     * Display a paginated, searchable list of hospitals.
     */
    public function index(Request $request)
    {
        $query = Hospital::query()
        ->orderByRaw("
            CASE
                WHEN EXISTS (
                    SELECT 1
                    FROM hospital_requests
                    WHERE hospital_requests.status = 'pending'
                    AND hospital_requests.hospital_id = hospitals.id
                )
                THEN 0
                ELSE 1
            END
        ")
        ->latest();
        $hospitals = $query->paginate(10)->withQueryString();
        // dd($hospitals);

        if ($search = $request->input('search')) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('hospital_id', 'like', "%{$search}%")
                ->orWhere('registration_number', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%");
        });
        }


        $hospitals = $query->paginate(10)->withQueryString();
    
        return view('admin.hospitals.hospitals', compact('hospitals'));
    }

    /**
     * Show the form for creating a new hospital.
     */
    public function create()
    {
        return view('admin.hospitals.hospitals-create');
    }

    /**
     * Validation rules shared between store() and update().
     * When $id is provided, the registration_number uniqueness check
     * ignores the current record.
     */
    protected function rules($id = null)
    {
        return [
            'name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255|unique:hospitals,registration_number' . ($id ? ",{$id}" : ''),
            'phone' => 'required|string|max:20|unique:hospitals,phone',
            'email' => 'required|email',
            'address' => 'required|string|max:500',
            'city' => 'string|max:100',
            'state' => 'string|max:100',
            'zip' => 'max:20',
            'country' => 'required|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'contact_person' => 'required|string|max:255',
        ];
    }

    /**
     * Store a newly created hospital.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Hospital::create([
        //     'name' => $request->input('name'),
        //     'hospital_id' => $this->generateHospitalId(),
        //     'registration_number' => $request->input('registration_number'),
        //     'phone' => $request->input('phone'),
        //     'address' => $request->input('address'),
        //     'city' => $request->input('city'),
        //     'state' => $request->input('state'),
        //     'zip' => $request->input('zip'),
        //     'country' => $request->input('country'),
        //     'latitude' => $request->input('latitude'),
        //     'longitude' => $request->input('longitude'),
        //     'contact_person' => $request->input('contact_person'),
        //     'created_by' => auth()->id(),
        // ]);


        


        // $validated = $request->validate([
        //     'hospital_name' => 'required|string|max:255',
        //     'hospital_registration' => 'required|unique:hospitals,registration_number|string|max:255',
        //     'hospital_email' => 'required|email|unique:users,email|max:255',
        //     'hospital_phone' => 'required|string|max:30',

        //     'hospital_address' => 'required|string',
        //     'hospital_city' => 'nullable|string|max:100',
        //     'hospital_state' => 'nullable|string|max:100',
        //     'hospital_zip' => 'nullable|string|max:20',
        //     'hospital_country' => 'nullable|string|max:100',

        //     'hospital_lat' => 'nullable|numeric|between:-90,90',
        //     'hospital_long' => 'nullable|numeric|between:-180,180',

        //     'hospital_contact_person' => 'required|string|max:255',
        // ]);

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
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($password),
            'role_id' => '3',  //For hospital
            'status' => 'active',
        ]);

        $hospital = Hospital::create([
            'name' => $request->name,
            'registration_number' => $request->registration_number ?? null,
            'hospital_id' => $user->id,
            'phone' => $request->phone ?? null,

            'address' => $request->address,
            'city' => $request->city ?? null,
            'state' => $request->state ?? null,
            'zip' => $request->zip ?? null,
            'country' => $request->country ?? null,

            'latitude' => $request->latitude ?? null,
            'longitude' => $request->longitude ?? null,

            'contact_person' => $request->contact_person ?? null,

            'created_by' => '1',
        ]);

        //send an email to the hospital with their credentials
        try {
            // print_r($submission);die;
            Mail::to($user->email)->send(new HospitalCreated($request->hospital_email,$password,$request->hospital_name));
        } catch (\Exception $e) {
            Log::error('Failed to send waitlist auto-responder: ' . $e->getMessage());
        }

        return redirect()->route('dashboard.hospitals')->with('success', 'Hospital created successfully.');
    }

    /**
     * Display the specified hospital, including any linked delivery items.
     */
    public function show($id)
    {
        $hospital = Hospital::with('items')->findOrFail($id);
        return view('admin.hospitals.hospitals-details', compact('hospital'));
    }

    /**
     * Show the form for editing the specified hospital.
     */
    public function edit($id)
    {
        $hospital = Hospital::findOrFail($id);
        return view('admin.hospitals.hospitals-edit', compact('hospital'));
    }

    /**
     * Update the specified hospital.
     */
    public function update(Request $request, $id)
    {
        $hospital = Hospital::findOrFail($id);

        $validator = Validator::make($request->all(), $this->rules($hospital->id));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $hospital->update($request->only([
            'name', 'registration_number', 'phone', 'address', 'city',
            'state', 'zip', 'country', 'latitude', 'longitude', 'contact_person',
        ]));

        return redirect()->route('dashboard.hospitals')->with('success', 'Hospital updated successfully.');
    }

    /**
     * Remove the specified hospital.
     */
    public function destroy($id)
    {
        $hospital = Hospital::findOrFail($id);
        $hospital->delete();

        return redirect()->route('dashboard.hospitals')->with('success', 'Hospital deleted successfully.');
    }

    /**
     * Generate a unique, human-readable hospital identifier (e.g. HOSP-00001).
     */
    protected function generateHospitalId()
    {
        $next = (int) Hospital::max('id') + 1;
        return 'HOSP-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}
