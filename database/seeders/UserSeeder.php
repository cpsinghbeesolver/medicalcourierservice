<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@medcourier.com',
                'role_id' => 1,
                'phone' => '+1-555-0100',
                'dob' => '1985-03-20',
                'address' => '456 Admin Ave, Los Angeles, CA 90001',
                'status' => 'active',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'coordinator@medcourier.com',
                'role_id' => 3,
                'phone' => '+1-555-0101',
                'status' => 'active',
            ],
            [
                'name' => 'Test Driver',
                'email' => 'driver@test.com',
                'role_id' => 4,
                'phone' => '+1-555-0199',
                'dob' => '1990-05-15',
                'address' => '123 Main Street, New York, NY 10001',
                'status' => 'active',
            ],
            [
                'name' => 'John Driver',
                'email' => 'john.driver@medcourier.com',
                'role_id' => 4,
                'phone' => '+1-555-0200',
                'status' => 'active',
            ],
            [
                'name' => 'Maria Rodriguez',
                'email' => 'maria.rodriguez@medcourier.com',
                'role_id' => 4,
                'phone' => '+1-555-0201',
                'status' => 'active',
            ],
            [
                'name' => 'David Chen',
                'email' => 'david.chen@medcourier.com',
                'role_id' => 4,
                'phone' => '+1-555-0202',
                'status' => 'active',
            ],
            [
                'name' => 'Emily Thompson',
                'email' => 'emily.thompson@medcourier.com',
                'role_id' => 4,
                'phone' => '+1-555-0203',
                'status' => 'active',
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@medcourier.com',
                'role_id' => 4,
                'phone' => '+1-555-0204',
                'status' => 'active',
            ],
            [
                'name' => 'Suspended Driver',
                'email' => 'suspended@medcourier.com',
                'role_id' => 4,
                'phone' => '+1-555-0299',
                'status' => 'suspended',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                array_merge($user, [
                    'password' => Hash::make('password123'),
                    'profile_photo' => null,
                    'email_verified_at' => now(),
                ])
            );
        }

        $this->command->info('Users seeded successfully.');
    }
}
