<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\DeliveryVerification;
use App\Models\DeliveryRequest;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coordinator = User::where('role_id', '3')->first();
        $drivers = User::where('role_id', '4')->where('status', 'active')->get();

        if (!$coordinator) {
            $this->command->error('No coordinator found. Please run UserSeeder first.');
            return;
        }

        if ($drivers->count() === 0) {
            $this->command->error('No active drivers found. Please run UserSeeder first.');
            return;
        }

        // Comprehensive Pickup Locations (Medical Facilities, Clinics, Hospitals)
        $pickupLocations = [
            [
                'name' => 'St. John Medical Center',
                'address' => '1923 South Utica Avenue',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip' => '74104',
                'phone' => '+1-918-744-2000',
                'lat' => 36.1335,
                'lng' => -95.9734,
                'contact' => 'Dr. Sarah Mitchell'
            ],
            [
                'name' => 'Mercy Hospital Oklahoma City',
                'address' => '4300 W Memorial Road',
                'city' => 'Oklahoma City',
                'state' => 'OK',
                'zip' => '73120',
                'phone' => '+1-405-755-1515',
                'lat' => 35.6080,
                'lng' => -97.5520,
                'contact' => 'Nurse Jane Cooper'
            ],
            [
                'name' => 'Texas Health Presbyterian Dallas',
                'address' => '8200 Walnut Hill Lane',
                'city' => 'Dallas',
                'state' => 'TX',
                'zip' => '75231',
                'phone' => '+1-214-345-6789',
                'lat' => 32.8829,
                'lng' => -96.7699,
                'contact' => 'Lab Coordinator Mike Johnson'
            ],
            [
                'name' => 'Baylor Scott & White Medical Center',
                'address' => '2401 South 31st Street',
                'city' => 'Temple',
                'state' => 'TX',
                'zip' => '76508',
                'phone' => '+1-254-724-2111',
                'lat' => 31.0754,
                'lng' => -97.3727,
                'contact' => 'Dr. Emily Rodriguez'
            ],
            [
                'name' => 'Norman Regional Health System',
                'address' => '901 N Porter Avenue',
                'city' => 'Norman',
                'state' => 'OK',
                'zip' => '73071',
                'phone' => '+1-405-307-1000',
                'lat' => 35.2226,
                'lng' => -97.4395,
                'contact' => 'Medical Staff Office'
            ],
            [
                'name' => 'Integris Baptist Medical Center',
                'address' => '3300 NW Expressway',
                'city' => 'Oklahoma City',
                'state' => 'OK',
                'zip' => '73112',
                'phone' => '+1-405-949-3011',
                'lat' => 35.5344,
                'lng' => -97.5584,
                'contact' => 'Lab Department'
            ],
        ];

        // Delivery Locations (Labs, Testing Facilities, Processing Centers)
        $deliveryLocations = [
            [
                'name' => 'Quest Diagnostics - Tulsa Lab',
                'address' => '6565 S Yale Avenue, Suite 400',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip' => '74136',
                'phone' => '+1-918-488-4900',
                'lat' => 36.0595,
                'lng' => -95.8947,
                'contact' => 'Lab Receiving'
            ],
            [
                'name' => 'LabCorp Dallas Regional Lab',
                'address' => '10101 Renner Boulevard',
                'city' => 'Lenexa',
                'state' => 'TX',
                'zip' => '75220',
                'phone' => '+1-214-358-9000',
                'lat' => 32.8654,
                'lng' => -96.8716,
                'contact' => 'Specimen Processing'
            ],
            [
                'name' => 'ARUP Laboratories Oklahoma',
                'address' => '500 Chipeta Way',
                'city' => 'Oklahoma City',
                'state' => 'OK',
                'zip' => '73104',
                'phone' => '+1-405-271-2600',
                'lat' => 35.4676,
                'lng' => -97.5164,
                'contact' => 'Central Receiving'
            ],
            [
                'name' => 'BioReference Laboratories Texas',
                'address' => '481 Edward H Ross Drive',
                'city' => 'Fort Worth',
                'state' => 'TX',
                'zip' => '76104',
                'phone' => '+1-817-332-4171',
                'lat' => 32.7357,
                'lng' => -97.3218,
                'contact' => 'Sample Intake'
            ],
            [
                'name' => 'Sonic Healthcare USA - Oklahoma',
                'address' => '8915 S Yale Avenue',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip' => '74137',
                'phone' => '+1-918-496-2200',
                'lat' => 36.0026,
                'lng' => -95.8964,
                'contact' => 'Lab Operations'
            ],
            [
                'name' => 'Pathology Associates Medical Lab',
                'address' => '110 W 7th Street',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip' => '74119',
                'phone' => '+1-918-579-2400',
                'lat' => 36.1540,
                'lng' => -95.9928,
                'contact' => 'Specimen Drop-off'
            ],
        ];

        $statuses = ['pending', 'assigned', 'in_transit', 'picked_up', 'delivered'];
        $priorities = ['normal', 'high', 'urgent'];
        $urgencyLevels = ['routine', 'stat', 'life_threatening'];
        $specimenTypes = ['1', '2', '3', '4'];
        $tempRequirements = ['1', '2', '3', '4'];

        $deliveryCount = 0;
        $itemCount = 0;
        $verificationCount = 0;

        // Create 20 deliveries with various statuses
        for ($i = 0; $i < 20; $i++) {
            $pickupIndex = $i % count($pickupLocations);
            $deliveryIndex = $i % count($deliveryLocations);
            $status = $statuses[$i % count($statuses)];
            $priority = $priorities[$i % count($priorities)];
            $urgency = $urgencyLevels[$i % count($urgencyLevels)];

            $pickupScheduled = now()->addDays(rand(-5, 10))->setHour(rand(8, 16))->setMinute(0);
            $deliveryScheduled = (clone $pickupScheduled)->addHours(rand(2, 6));

            // Time windows
            $timeWindowStart = (clone $pickupScheduled)->subMinutes(30);
            $timeWindowEnd = (clone $deliveryScheduled)->addMinutes(30);

            // Generate patient initials
            $patientInitials = chr(rand(65, 90)) . chr(rand(65, 90));

            // Generate specimen ID
            $specimenId = 'SPEC-' . date('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);

            $deliveryData = [
                'delivery_number' => 'DLV-' . date('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'created_by' => $coordinator->id,

                // PHI Data
                'specimen_id' => $specimenId,
                'patient_initials' => $patientInitials,
                'urgency_level' => $urgency,

                // Pickup Information
                'pickup_name' => $pickupLocations[$pickupIndex]['name'],
                'pickup_address' => $pickupLocations[$pickupIndex]['address'],
                'pickup_city' => $pickupLocations[$pickupIndex]['city'],
                'pickup_state' => $pickupLocations[$pickupIndex]['state'],
                'pickup_zip' => $pickupLocations[$pickupIndex]['zip'],
                'pickup_phone' => $pickupLocations[$pickupIndex]['phone'],
                'pickup_contact_person' => $pickupLocations[$pickupIndex]['contact'],
                'pickup_latitude' => $pickupLocations[$pickupIndex]['lat'],
                'pickup_longitude' => $pickupLocations[$pickupIndex]['lng'],
                'pickup_scheduled_time' => $pickupScheduled,

                // Delivery Information
                'delivery_name' => $deliveryLocations[$deliveryIndex]['name'],
                'delivery_address' => $deliveryLocations[$deliveryIndex]['address'],
                'delivery_city' => $deliveryLocations[$deliveryIndex]['city'],
                'delivery_state' => $deliveryLocations[$deliveryIndex]['state'],
                'delivery_zip' => $deliveryLocations[$deliveryIndex]['zip'],
                'delivery_phone' => $deliveryLocations[$deliveryIndex]['phone'],
                'delivery_contact_person' => $deliveryLocations[$deliveryIndex]['contact'],
                'delivery_latitude' => $deliveryLocations[$deliveryIndex]['lat'],
                'delivery_longitude' => $deliveryLocations[$deliveryIndex]['lng'],
                'delivery_scheduled_time' => $deliveryScheduled,

                // Time Windows
                'scheduled_time_window_start' => $timeWindowStart,
                'scheduled_time_window_end' => $timeWindowEnd,

                'status' => $status,
                'priority' => $priority,

                // Vehicle Requirements
                'required_vehicle_type' => $i % 3 === 0 ? 'refrigerated_van' : null,

                // Digital Chain of Custody
                //'requires_barcode_scan' => $i % 2 === 0,
                //'requires_signature_or_photo' => true,

                'special_instructions' => $i % 3 === 0 ? 'Handle with care. Time-sensitive materials. Enter through North Dock.' : null,
                'notes' => 'Job Title: ' . $pickupLocations[$pickupIndex]['name'] . ' Morning Run',
            ];

            // Assign driver and dispatch time for non-pending deliveries
            if ($status !== 'pending') {
                $deliveryData['driver_id'] = $drivers->random()->id;
                $deliveryData['dispatched_at'] = (clone $pickupScheduled)->subHours(2);
            }

            // Add accepted time if assigned
            if (in_array($status, ['assigned', 'in_transit', 'picked_up', 'delivered'])) {
                $deliveryData['accepted_by_driver_at'] = (clone $pickupScheduled)->subHours(1);
            }

            // Add actual times for picked_up/in_transit/delivered deliveries
            if (in_array($status, ['picked_up', 'in_transit', 'delivered'])) {
                $deliveryData['pickup_actual_time'] = (clone $pickupScheduled)->addMinutes(rand(-15, 15));
            }

            if ($status === 'delivered') {
                $deliveryData['delivery_actual_time'] = (clone $deliveryScheduled)->addMinutes(rand(-30, 30));
            }

            // Calculate distance and duration (OK-TX border runs can be 100+ miles)
            $isLongDistance = $pickupLocations[$pickupIndex]['state'] !== $deliveryLocations[$deliveryIndex]['state'];
            $deliveryData['distance_km'] = $isLongDistance ? round(rand(150, 300), 2) : round(rand(10, 80), 2);
            $deliveryData['estimated_duration_minutes'] = $isLongDistance ? rand(120, 240) : rand(20, 90);

            $delivery = Delivery::updateOrCreate(
                [
                    'delivery_number' => $deliveryData['delivery_number'],
                ],
                $deliveryData
            );
            $deliveryCount++;

            // Create 1-3 delivery items for each delivery
            $numItems = rand(1, 3);
            for ($j = 0; $j < $numItems; $j++) {
                $specimenType = $specimenTypes[array_rand($specimenTypes)];
                $tempReq = $tempRequirements[array_rand($tempRequirements)];
                $numDrop = rand(0, 4);
                DeliveryItem::updateOrCreate(
                    [
                        'delivery_id' => $delivery->id,
                        'barcode' => 'BC-' . date('Ymd') . '-' . str_pad(($i * 10 + $j + 1), 6, '0', STR_PAD_LEFT),
                    ],
                    [
                        'item_type' => 'specimen',
                        'specimen_type' => $specimenType,
                        'barcode' => 'BC-' . date('Ymd') . '-' . str_pad(($i * 10 + $j + 1), 6, '0', STR_PAD_LEFT),
                        'quantity' => rand(1, 5),
                        'description' => ucfirst($specimenType) . ' specimen for testing',
                        'temperature_requirement' => $tempReq,
                        'requires_special_handling' => in_array($tempReq, ['frozen', 'dry_ice']),
                        'handling_instructions' => $tempReq === 'dry_ice'
                            ? 'Must maintain dry ice temperature. Handle with insulated gloves.'
                            : null,
                        'status' => $status === 'delivered'
                            ? 'delivered'
                            : ($status === 'picked_up' ? 'collected' : 'pending'),
                        'dropoff_name' => $deliveryLocations[$numDrop]['name'],
                        'dropoff_address' => $deliveryLocations[$numDrop]['address'],
                        'dropoff_city' => $deliveryLocations[$numDrop]['city'],
                        'dropoff_state' => $deliveryLocations[$numDrop]['state'],
                        'dropoff_zip' => $deliveryLocations[$numDrop]['zip'],
                        'dropoff_phone' => $deliveryLocations[$numDrop]['phone'],
                        'dropoff_contact_person' => $deliveryLocations[$numDrop]['contact'],
                        'dropoff_latitude' => $deliveryLocations[$numDrop]['lat'],
                        'dropoff_longitude' => $deliveryLocations[$numDrop]['lng'],
                    ]
                );
                $itemCount++;
            }

            // Create verifications for picked_up and delivered deliveries
            if (in_array($status, ['picked_up', 'delivered'])) {
                // Pickup verification
                DeliveryVerification::updateOrCreate(
                    [
                        'delivery_id' => $delivery->id,
                        'verification_type' => 'pickup',
                    ],
                    [
                        'recipient_name' => $pickupLocations[$pickupIndex]['contact'],
                        'signature_image' => 'data:image/png;base64,...',
                        'photo_proof' => null,
                        'latitude' => $pickupLocations[$pickupIndex]['lat'] + (rand(-100, 100) / 10000),
                        'longitude' => $pickupLocations[$pickupIndex]['lng'] + (rand(-100, 100) / 10000),
                        'notes' => 'Specimen picked up and sealed',
                        'verified_at' => $delivery->pickup_actual_time,
                    ]
                );
                $verificationCount++;
            }

            if ($status === 'delivered') {
                // Delivery verification
                DeliveryVerification::create([
                    'delivery_id' => $delivery->id,
                    'verification_type' => 'delivery',
                    'recipient_name' => $deliveryLocations[$deliveryIndex]['contact'],
                    'signature_image' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
                    'photo_proof' => null,
                    'latitude' => $deliveryLocations[$deliveryIndex]['lat'] + (rand(-100, 100) / 10000),
                    'longitude' => $deliveryLocations[$deliveryIndex]['lng'] + (rand(-100, 100) / 10000),
                    'notes' => 'Specimen delivered and received in good condition',
                    'verified_at' => $delivery->delivery_actual_time,
                ]);
                $verificationCount++;
            }

            if($delivery->driver_id){
                DeliveryRequest::updateOrCreate(
                    [
                        'delivery_id' => $delivery->id,
                        'driver_id' => $delivery->driver_id,
                    ],
                    [
                        'requested_at' => now(),
                        'status' => 'pending',
                    ]
                    );
            }
        }

        $this->command->info("✓ Created {$deliveryCount} deliveries with {$itemCount} items and {$verificationCount} verifications");
        $this->command->info("✓ Locations: " . count($pickupLocations) . " pickup facilities, " . count($deliveryLocations) . " delivery labs");
        $this->command->info("✓ Coverage: Oklahoma & Texas medical facilities");
    }
}
