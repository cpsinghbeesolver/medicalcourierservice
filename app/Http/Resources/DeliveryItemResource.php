<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryItemResource extends JsonResource
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
            'item_type' => $this->item_type,
            'item_code' => $this->item_code,
            'item_name' => $this->item_name,
            'specimen_type' => $this->specimen_type,
            'specimen_name' => optional($this->specimenType)->name ?? $this->item_name,
            'barcode' => $this->barcode,
            'quantity' => $this->quantity,
            'description' => $this->description,
            'temperature_requirement' => $this->temperature_requirement,
            'temperature_requirement_name' => optional($this->tempratureRequirement)->name,
            'requires_special_handling' => (bool) $this->requires_special_handling,
            'handling_instructions' => $this->handling_instructions,
            'status' => $this->status,
            'dropoff_name' => $this->dropoff_name,
            'dropoff_address' => $this->dropoff_address,
            'dropoff_phone' => $this->dropoff_phone,
            'dropoff_contact_person' => $this->dropoff_contact_person,
            'signature_image' => $this->signature_image,
            'photo_proof' => $this->photo_proof
        ];
    }
}
