<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\TemperatureRequirement;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSpecimenTempVehicleRequest;

class TemperatureRequirementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $TemperatureRequirements = TemperatureRequirement::where('company_id', auth()->id())->latest()->paginate(15);
        return view('company/temperature-requirement.index', compact('TemperatureRequirements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('company/temperature-requirement.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSpecimenTempVehicleRequest $request)
    {
        TemperatureRequirement::create($request->validated());

        return redirect()
            ->route('temperature-requirement.index')
            ->with('success', 'Temperature Requirement created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TemperatureRequirement $TemperatureRequirement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TemperatureRequirement $TemperatureRequirement)
    {
        return view('company/temperature-requirement.edit', compact('TemperatureRequirement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TemperatureRequirement $TemperatureRequirement)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required'
        ]);
        $TemperatureRequirement->update($validated);

        return redirect()
            ->route('temperature-requirement.index')
            ->with('success', 'Temperature Requirement updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TemperatureRequirement $TemperatureRequirement)
    {
        $TemperatureRequirement->delete();

        return redirect()
            ->route('temperature-requirement.index')
            ->with('success', 'Temperature Requirement deleted successfully.');
    }
}
