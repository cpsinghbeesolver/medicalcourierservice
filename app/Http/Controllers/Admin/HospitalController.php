<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hospital;
use Illuminate\Support\Facades\Validator;

class HospitalController extends Controller
{
    /**
     * Display a paginated, searchable list of hospitals.
     */
    public function index(Request $request)
    {
        $query = Hospital::latest();

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
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip' => 'required|string|max:20',
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

        Hospital::create([
            'name' => $request->input('name'),
            'hospital_id' => $this->generateHospitalId(),
            'registration_number' => $request->input('registration_number'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'zip' => $request->input('zip'),
            'country' => $request->input('country'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'contact_person' => $request->input('contact_person'),
            'created_by' => auth()->id(),
        ]);

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
