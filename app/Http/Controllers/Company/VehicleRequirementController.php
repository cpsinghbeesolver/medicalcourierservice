<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\VehicleRequirement;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSpecimenTempVehicleRequest;

class VehicleRequirementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $VehicleRequirements = VehicleRequirement::where('company_id', auth()->id())->latest()->paginate(15);
        return view('company/vehicle-requirement.index', compact('VehicleRequirements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('company/vehicle-requirement.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSpecimenTempVehicleRequest $request)
    {
        VehicleRequirement::create($request->validated());

        return redirect()
            ->route('vehicle-requirement.index')
            ->with('success', 'Vehicle Requirement created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(VehicleRequirement $VehicleRequirement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehicleRequirement $VehicleRequirement)
    {
        return view('company/vehicle-requirement.edit', compact('VehicleRequirement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VehicleRequirement $VehicleRequirement)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required'
        ]);
        $VehicleRequirement->update($validated);

        return redirect()
            ->route('vehicle-requirement.index')
            ->with('success', 'Vehicle Requirement updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleRequirement $VehicleRequirement)
    {
        $VehicleRequirement->delete();

        return redirect()
            ->route('vehicle-requirement.index')
            ->with('success', 'Vehicle Requirement deleted successfully.');
    }
}

