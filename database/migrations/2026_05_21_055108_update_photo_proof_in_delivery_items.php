<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('delivery_items', function (Blueprint $table) {
            DB::statement("
            ALTER TABLE `delivery_items` ADD `photo_proof` longtext NULL DEFAULT NULL AFTER `notes`;
            ");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_items', function (Blueprint $table) {
            DB::statement("
            ALTER TABLE `delivery_items` DROP COLUMN `photo_proof`;
            ");
        });
    }
};
