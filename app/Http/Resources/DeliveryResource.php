<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
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
            'delivery_number' => $this->delivery_number,
            'status' => $this->status,
            'priority' => $this->priority,
            'pickup' => [
                'name' => $this->pickup_name,
                'address' => $this->pickup_address,
                'city' => $this->pickup_city,
                'state' => $this->pickup_state,
                'zip' => $this->pickup_zip,
                'phone' => $this->pickup_phone,
                'contact_person' => $this->pickup_contact_person,
                'location' => [
                    'latitude' => $this->pickup_latitude,
                    'longitude' => $this->pickup_longitude,
                ],
                'scheduled_time' => $this->pickup_scheduled_time?->toIso8601String(),
                'actual_time' => $this->pickup_actual_time?->toIso8601String(),
            ],
            'delivery' => [
                'name' => $this->delivery_name,
                'address' => $this->delivery_address,
                'city' => $this->delivery_city,
                'state' => $this->delivery_state,
                'zip' => $this->delivery_zip,
                'phone' => $this->delivery_phone,
                'contact_person' => $this->delivery_contact_person,
                'location' => [
                    'latitude' => $this->delivery_latitude,
                    'longitude' => $this->delivery_longitude,
                ],
                'scheduled_time' => $this->delivery_scheduled_time?->toIso8601String(),
                'actual_time' => $this->delivery_actual_time?->toIso8601String(),
            ],
            'urgency_level' => $this->urgency_level,
            'container_count' => $this->container_count,
            'required_vehicle_type' => $this->required_vehicle_type,
            'vehicle_requirement' => $this->whenLoaded('vehicleRequirement', function () {
                return [
                    'id' => $this->vehicleRequirement->id,
                    'name' => $this->vehicleRequirement->name,
                ];
            }),
            'scheduled_time_window_start' => $this->scheduled_time_window_start?->toIso8601String(),
            'scheduled_time_window_end' => $this->scheduled_time_window_end?->toIso8601String(),
            'dispatched_at' => $this->dispatched_at?->toIso8601String(),
            'accepted_by_driver_at' => $this->accepted_by_driver_at?->toIso8601String(),
            'requires_pickup_signature' => (bool) $this->requires_pickup_signature,
            'requires_pickup_photo' => (bool) $this->requires_pickup_photo,
            'requires_pickup_barcode_scan' => (bool) $this->requires_pickup_barcode_scan,
            'requires_dropoff_signature' => (bool) $this->requires_dropoff_signature,
            'requires_dropoff_photo' => (bool) $this->requires_dropoff_photo,
            'requires_dropoff_barcode_scan' => (bool) $this->requires_dropoff_barcode_scan,
            'requires_recepient_id_scan' => (bool) $this->requires_recepient_id_scan,
            'special_instructions' => $this->special_instructions,
            'notes' => $this->notes,
            'distance_km' => $this->distance_km,
            'estimated_duration_minutes' => $this->estimated_duration_minutes,
            'driver' => $this->whenLoaded('driver', function () {
                return [
                    'id' => $this->driver->id,
                    'name' => $this->driver->name,
                    'phone' => $this->driver->phone,
                    'email' => $this->driver->email,
                ];
            }),
            'items' => DeliveryItemResource::collection($this->whenLoaded('items')),
            'verifications' => DeliveryVerificationResource::collection($this->whenLoaded('verifications')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
