<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'phone' => $this->phone,
            'dob' => $this->dob?->format('Y-m-d'),
            'address' => $this->address,
            'profile_photo' => $this->profile_photo,
            'status' => $this->status,
            'last_login_at' => $this->last_login_at?->toIso8601String(),
            'driver_profile' => $this->whenLoaded('driverProfile', function () {
                return new DriverProfileResource($this->driverProfile);
            }),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
