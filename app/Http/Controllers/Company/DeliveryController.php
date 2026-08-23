<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Delivery;
use App\Models\SpecimenType;
use App\Models\TemperatureRequirement;
use App\Models\VehicleRequirement;

class DeliveryController extends Controller
{
    public function index($id)
    {
        $delivery = Delivery::where('id',$id)->first();
        $delivery_id = $id;
        // $delivery->refresh();
        $specimenTypes = SpecimenType::where('company_id', auth()->id())->where('status',1)->orderBy('name')->get();
        $temperatureRequirements = TemperatureRequirement::where('company_id', auth()->id())->where('status',1)->orderBy('name')->get();
        $vehicleRequirements = VehicleRequirement::where('company_id', auth()->id())->where('status',1)->orderBy('name')->get();
        return view('company.job-edit', compact('delivery_id','specimenTypes','temperatureRequirements','vehicleRequirements','delivery'));
    }
}
