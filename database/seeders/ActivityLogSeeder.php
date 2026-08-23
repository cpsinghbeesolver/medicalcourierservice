<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Delivery;
use App\Models\ActivityLog;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $deliveries = Delivery::all();

        if ($users->count() === 0) {
            $this->command->error('No users found. Please run UserSeeder first.');
            return;
        }

        $actions = [
            'created',
            'updated',
            'deleted',
            'viewed',
            'assigned',
            'status_changed',
            'location_updated',
            'verified',
        ];

        $activityCount = 0;

        // Create activity logs for users
        foreach ($users as $user) {
            // Login activities
            for ($i = 0; $i < rand(3, 8); $i++) {
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'login',
                    'model_type' => 'App\Models\User',
                    'model_id' => $user->id,
                    'description' => "{$user->name} logged in",
                    'ip_address' => '192.168.1.' . rand(1, 255),
                    'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15',
                    'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                ]);
                $activityCount++;
            }

            // Profile update activities
            if (rand(0, 1)) {
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'updated',
                    'model_type' => 'App\Models\User',
                    'model_id' => $user->id,
                    'description' => "{$user->name} updated their profile",
                    'ip_address' => '192.168.1.' . rand(1, 255),
                    'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15',
                    'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                ]);
                $activityCount++;
            }
        }

        // Create activity logs for deliveries
        foreach ($deliveries as $delivery) {
            // Created
            ActivityLog::create([
                'user_id' => $delivery->created_by,
                'action' => 'created',
                'model_type' => 'App\Models\Delivery',
                'model_id' => $delivery->id,
                'description' => "Created delivery {$delivery->delivery_number}",
                'ip_address' => '192.168.1.' . rand(1, 255),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => $delivery->created_at,
            ]);
            $activityCount++;

            // Assigned
            if ($delivery->driver_id) {
                $driver = User::find($delivery->driver_id);
                ActivityLog::create([
                    'user_id' => $delivery->created_by,
                    'action' => 'assigned',
                    'model_type' => 'App\Models\Delivery',
                    'model_id' => $delivery->id,
                    'description' => "Assigned delivery {$delivery->delivery_number} to {$driver->name}",
                    'ip_address' => '192.168.1.' . rand(1, 255),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'created_at' => $delivery->created_at->addMinutes(rand(5, 30)),
                ]);
                $activityCount++;

                // Driver viewed
                ActivityLog::create([
                    'user_id' => $delivery->driver_id,
                    'action' => 'viewed',
                    'model_type' => 'App\Models\Delivery',
                    'model_id' => $delivery->id,
                    'description' => "{$driver->name} viewed delivery {$delivery->delivery_number}",
                    'ip_address' => '192.168.1.' . rand(1, 255),
                    'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15',
                    'created_at' => $delivery->created_at->addMinutes(rand(35, 60)),
                ]);
                $activityCount++;
            }

            // Status changes
            if ($delivery->status === 'in_transit' || $delivery->status === 'completed') {
                ActivityLog::create([
                    'user_id' => $delivery->driver_id,
                    'action' => 'status_changed',
                    'model_type' => 'App\Models\Delivery',
                    'model_id' => $delivery->id,
                    'description' => "Changed delivery {$delivery->delivery_number} status to in_transit",
                    'ip_address' => '192.168.1.' . rand(1, 255),
                    'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15',
                    'created_at' => $delivery->pickup_actual_time ?? $delivery->created_at->addHours(rand(1, 3)),
                ]);
                $activityCount++;

                // Location updates
                for ($i = 0; $i < rand(3, 8); $i++) {
                    ActivityLog::create([
                        'user_id' => $delivery->driver_id,
                        'action' => 'location_updated',
                        'model_type' => 'App\Models\Delivery',
                        'model_id' => $delivery->id,
                        'description' => "Updated location for delivery {$delivery->delivery_number}",
                        'ip_address' => '192.168.1.' . rand(1, 255),
                        'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15',
                        'created_at' => ($delivery->pickup_actual_time ?? $delivery->created_at)->addMinutes(rand(10, 120)),
                    ]);
                    $activityCount++;
                }
            }

            if ($delivery->status === 'completed') {
                ActivityLog::create([
                    'user_id' => $delivery->driver_id,
                    'action' => 'status_changed',
                    'model_type' => 'App\Models\Delivery',
                    'model_id' => $delivery->id,
                    'description' => "Completed delivery {$delivery->delivery_number}",
                    'ip_address' => '192.168.1.' . rand(1, 255),
                    'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15',
                    'created_at' => $delivery->delivery_actual_time ?? $delivery->created_at->addHours(rand(3, 6)),
                ]);
                $activityCount++;
            }

            if ($delivery->status === 'cancelled') {
                ActivityLog::create([
                    'user_id' => $delivery->created_by,
                    'action' => 'status_changed',
                    'model_type' => 'App\Models\Delivery',
                    'model_id' => $delivery->id,
                    'description' => "Cancelled delivery {$delivery->delivery_number}",
                    'ip_address' => '192.168.1.' . rand(1, 255),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'created_at' => $delivery->created_at->addHours(rand(1, 48)),
                ]);
                $activityCount++;
            }
        }

        $this->command->info("Created {$activityCount} activity log entries");
    }
}
