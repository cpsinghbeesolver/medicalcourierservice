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
        Schema::table('specimen_types', function (Blueprint $table) {
            // Add the column first
            $table->unsignedBigInteger('company_id')->after('id');

            // Then add the foreign key
            $table->foreign('company_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        Schema::table('temperature_requirements', function (Blueprint $table) {
            // Add the column first
            $table->unsignedBigInteger('company_id')->after('id');

            // Then add the foreign key
            $table->foreign('company_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        Schema::table('vehicle_requirements', function (Blueprint $table) {
            // Add the column first
            $table->unsignedBigInteger('company_id')->after('id');

            // Then add the foreign key
            $table->foreign('company_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('specimen_types', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
        Schema::table('temperature_requirements', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
        Schema::table('vehicle_requirements', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
