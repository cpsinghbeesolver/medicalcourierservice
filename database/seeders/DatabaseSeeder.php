<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Starting database seeding...');
        $this->command->newLine();

        // Seed in order of dependencies
        $this->call([
            RolesTableSeeder::class,
            UserSeeder::class,
            DriverProfileSeeder::class,
            //DeliverySeeder::class,
            ActivityLogSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('Database seeding completed successfully!');
        $this->command->newLine();
        $this->command->info('Test Credentials:');
        $this->command->info('─────────────────────────────────────────────');
        $this->command->info('Admin:       admin@medcourier.com / password123');
        $this->command->info('Coordinator: coordinator@medcourier.com / password123');
        $this->command->info('Driver:      john.driver@medcourier.com / password123');
        $this->command->info('─────────────────────────────────────────────');
    }
}
