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
        Schema::table('safety_checklists', function (Blueprint $table) {
            // Add the 6 missing fields that should be in the schema
            // $table->boolean('lights_functional')->default(false);
            // $table->boolean('tire_pressure_checked')->default(false);
           // $table->boolean('id_badge_visible')->default(false);
           // $table->boolean('biohazard_bags_available')->default(false);
          //  $table->boolean('secure_transport_containers')->default(false);
          //  $table->boolean('gloves_available')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('safety_checklists', function (Blueprint $table) {
            $table->dropColumn([
                // 'lights_functional',
                // 'tire_pressure_checked',
                // 'id_badge_visible',
             //   'biohazard_bags_available',
              //  'secure_transport_containers',
            //    'gloves_available',
            ]);
        });
    }
};
