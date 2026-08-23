<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DriverProfile;
use Illuminate\Database\Seeder;

class DriverProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all driver users
        $drivers = User::where('role_id', '4')
            ->where('status', 'active')
            ->get();

        if ($drivers->count() === 0) {
            $this->command->error('No active driver users found. Please run UserSeeder first.');
            return;
        }

        $vehicles = [
            'refrigerated_van' => 'Refrigerated Van',
            'standard_van' => 'Standard Van',
            'temperature_controlled' => 'Temperature Controlled Van',
            'standard_van' => 'Standard Van',
            'refrigerated_van' => 'Refrigerated Van',
        ];

        $vehicleTypes = array_keys($vehicles);
        $plates = ['ABC-1234', 'XYZ-5678', 'DEF-9012', 'GHI-3456', 'JKL-7890'];

        $addresses = [
            ['street' => '123 Oak Street', 'city' => 'New York', 'state' => 'NY', 'zip' => '10001'],
            ['street' => '456 Maple Avenue', 'city' => 'Brooklyn', 'state' => 'NY', 'zip' => '11201'],
            ['street' => '789 Pine Road', 'city' => 'Queens', 'state' => 'NY', 'zip' => '11354'],
            ['street' => '321 Elm Boulevard', 'city' => 'Bronx', 'state' => 'NY', 'zip' => '10451'],
            ['street' => '654 Cedar Lane', 'city' => 'Manhattan', 'state' => 'NY', 'zip' => '10002'],
        ];

        $emergencyContacts = [
            ['name' => 'Jane Driver', 'phone' => '+1-555-1000'],
            ['name' => 'Carlos Rodriguez', 'phone' => '+1-555-1001'],
            ['name' => 'Lisa Chen', 'phone' => '+1-555-1002'],
            ['name' => 'Robert Thompson', 'phone' => '+1-555-1003'],
            ['name' => 'Sarah Brown', 'phone' => '+1-555-1004'],
        ];

        $latitudes = [40.7128, 40.6782, 40.7282, 40.8448, 40.7589];
        $longitudes = [-74.0060, -73.9442, -73.7949, -73.8648, -73.9851];

        $states = ['NY', 'NJ', 'CT', 'PA', 'NY'];
        $backgroundStatuses = ['Clear', 'Clear', 'Clear', 'Pending', 'Clear'];

        foreach ($drivers as $index => $driver) {
            $vehicleIndex = $index % count($vehicleTypes);
            $addressIndex = $index % count($addresses);

            // First 3 drivers are clocked in, others are off duty
            $isClockedIn = $index < 3;
            $availabilityStatus = $isClockedIn ? ($index < 2 ? 'available' : 'busy') : 'off_duty';

            DriverProfile::create([
                'user_id' => $driver->id,
                'license_number' => 'DL' . str_pad($index + 1000000, 9, '0', STR_PAD_LEFT),
                'license_expiry_date' => now()->addYears(rand(1, 3))->format('Y-m-d'),
                'license_state' => $states[$index % count($states)],
                'date_of_birth' => now()->subYears(rand(25, 55))->format('Y-m-d'),
                'vehicle_type' => $vehicleTypes[$vehicleIndex],
                'vehicle_plate_number' => $plates[$vehicleIndex],
                'address' => $addresses[$addressIndex]['street'],
                'city' => $addresses[$addressIndex]['city'],
                'state' => $addresses[$addressIndex]['state'],
                'zip_code' => $addresses[$addressIndex]['zip'],

                // Medical Compliance Fields
                'insurance_policy_number' => 'INS-' . str_pad($index + 100000, 8, '0', STR_PAD_LEFT),
                'insurance_expiry_date' => now()->addMonths(rand(6, 18))->format('Y-m-d'),
                'hipaa_certification_date' => now()->subMonths(rand(1, 11))->format('Y-m-d'),
                'hipaa_certification_file' => $index < 4 ? 'certificates/hipaa_cert_' . ($index + 1) . '.pdf' : null,
                'bloodborne_pathogen_training_date' => now()->subMonths(rand(1, 11))->format('Y-m-d'),
                'bloodborne_pathogen_file' => $index < 3 ? 'certificates/bbp_cert_' . ($index + 1) . '.pdf' : null,
                'specimen_handling_certification_date' => now()->subMonths(rand(1, 6))->format('Y-m-d'),
                'specimen_handling_confirmed' => true,
                'background_check_status' => $backgroundStatuses[$index % count($backgroundStatuses)],
                'drug_screen_expiry' => now()->addMonths(rand(3, 12))->format('Y-m-d'),

                'emergency_contact_name' => $emergencyContacts[$addressIndex]['name'],
                'emergency_contact_phone' => $emergencyContacts[$addressIndex]['phone'],
                'availability_status' => $availabilityStatus,
                'current_latitude' => $latitudes[$addressIndex],
                'current_longitude' => $longitudes[$addressIndex],
                'is_clocked_in' => $isClockedIn,
                'clocked_in_at' => $isClockedIn ? now()->subHours(rand(1, 4)) : null,
                'last_location_update' => $isClockedIn ? now()->subMinutes(rand(1, 30)) : null,
            ]);
        }

        $this->command->info('Created ' . $drivers->count() . ' driver profiles with medical compliance data');
    }
}
