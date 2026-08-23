<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryVerificationResource extends JsonResource
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
            'verification_type' => $this->verification_type,
            'recipient_name' => $this->recipient_name,
            'signature_image' => $this->signature_image,
            'photo_proof' => $this->photo_proof,
            'verified_at' => $this->verified_at?->toIso8601String(),
            'location' => [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],
            'notes' => $this->notes,
        ];
    }
}
