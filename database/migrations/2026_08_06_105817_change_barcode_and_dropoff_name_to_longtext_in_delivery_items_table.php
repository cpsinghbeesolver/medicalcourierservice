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
            $table->string('barcode', 500)->nullable()->change();
            $table->string('dropoff_name', 500)->nullable()->change();    
            $table->string('dropoff_address', 500)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_items', function (Blueprint $table) {
            $table->text('barcode')->nullable()->change();
            $table->text('dropoff_address')->nullable()->change();
            $table->text('dropoff_name')->nullable()->change();
        });
    }
};
