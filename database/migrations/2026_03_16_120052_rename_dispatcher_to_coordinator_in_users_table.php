<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Modify the ENUM to include 'coordinator' temporarily
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'driver', 'dispatcher', 'coordinator') NOT NULL DEFAULT 'driver'");

        // Step 2: Update all users with role 'dispatcher' to 'coordinator'
        DB::table('users')
            ->where('role', 'dispatcher')
            ->update(['role' => 'coordinator']);

        // Step 3: Remove 'dispatcher' from the ENUM
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'driver', 'coordinator') NOT NULL DEFAULT 'driver'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Add 'dispatcher' back to the ENUM
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'driver', 'coordinator', 'dispatcher') NOT NULL DEFAULT 'driver'");

        // Step 2: Update all users with role 'coordinator' back to 'dispatcher'
        DB::table('users')
            ->where('role', 'coordinator')
            ->update(['role' => 'dispatcher']);

        // Step 3: Remove 'coordinator' from the ENUM
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'driver', 'dispatcher') NOT NULL DEFAULT 'driver'");
    }
};
