<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SpecimenType;
use App\Models\TemperatureRequirement;
use App\Models\VehicleRequirement;

class CompanyDashboardController extends Controller
{
     // This controller is used for Conpamy dashboard activites 
    public function index()
    {

    }

    public function createJob(){
        $specimenTypes = SpecimenType::where('company_id', auth()->id())->where('status',1)->orderBy('name')->get();
        $temperatureRequirements = TemperatureRequirement::where('company_id', auth()->id())->where('status',1)->orderBy('name')->get();
        $vehicleRequirements = VehicleRequirement::where('company_id', auth()->id())->where('status',1)->orderBy('name')->get();
        return view('company.job-create', compact('specimenTypes','temperatureRequirements','vehicleRequirements'));
    }
}
