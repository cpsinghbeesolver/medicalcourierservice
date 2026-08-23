<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'phone' => $this->user->phone,
                    'role' => $this->user->role,
                    'status' => $this->user->status,
                ];
            }),
            'license_number' => $this->license_number,
            'license_expiry_date' => $this->license_expiry_date?->format('Y-m-d'),
            'license_expired' => $this->isLicenseExpired(),
            'vehicle_type' => $this->vehicle_type,
            'vehicle_plate_number' => $this->vehicle_plate_number,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'availability_status' => $this->availability_status,
            'current_location' => [
                'latitude' => $this->current_latitude,
                'longitude' => $this->current_longitude,
            ],
            'insurance_policy_number' => $this->insurance_policy_number,
            'insurance_expiry_date' => $this->license_expiry_date?->format('Y-m-d'),
            'hipaa_certification_date' => $this->hipaa_certification_date?->format('Y-m-d'),
            'hipaa_certification_file' => $this->hipaa_certification_file,
            'background_check_status' => $this->background_check_status,
            'drug_screen_expiry' => $this->drug_screen_expiry?->format('Y-m-d'),
            'specimen_handling_certification_date' => $this->specimen_handling_certification_date?->format('Y-m-d'),
            'specimen_handling_confirmed' => $this->specimen_handling_confirmed,
            'bloodborne_pathogen_training_date' => $this->bloodborne_pathogen_training_date?->format('Y-m-d'),
            'bloodborne_pathogen_file' => $this->bloodborne_pathogen_file
        ];
    }
}
