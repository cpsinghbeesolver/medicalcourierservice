<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\SpecimenType;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSpecimenTempVehicleRequest;

class SpecimenTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $specimenTypes = SpecimenType::where('company_id', auth()->id())->latest()->paginate(15);
        return view('company/specimen-types.index', compact('specimenTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('company/specimen-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSpecimenTempVehicleRequest $request)
    {
        SpecimenType::create($request->validated());

        return redirect()
            ->route('specimen-types.index')
            ->with('success', 'Specimen type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SpecimenType $specimenType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SpecimenType $specimenType)
    {
        return view('company/specimen-types.edit', compact('specimenType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SpecimenType $specimenType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required'
        ]);
        $specimenType->update($validated);

        return redirect()
            ->route('specimen-types.index')
            ->with('success', 'Specimen type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SpecimenType $specimenType)
    {
        $specimenType->delete();

        return redirect()
            ->route('specimen-types.index')
            ->with('success', 'Specimen Type deleted successfully.');
    }
}
