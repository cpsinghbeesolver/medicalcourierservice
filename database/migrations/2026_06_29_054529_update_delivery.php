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
        Schema::table('deliveries', function (Blueprint $table) {
            $table->text('delivery_city')->nullable()->change();
            $table->text('delivery_state')->nullable()->change();
            $table->text('delivery_zip')->nullable()->change();
            $table->text('delivery_phone')->nullable()->change();

            $table->text('pickup_city')->nullable()->change();
            $table->text('pickup_state')->nullable()->change();
            $table->text('pickup_zip')->nullable()->change();
            $table->text('pickup_phone')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->text('delivery_city')->nullable()->change();
            $table->text('delivery_state')->nullable()->change();
            $table->text('delivery_zip')->nullable()->change();
            $table->text('delivery_phone')->nullable()->change();

            $table->text('pickup_city')->nullable()->change();
            $table->text('pickup_state')->nullable()->change();
            $table->text('pickup_zip')->nullable()->change();
            $table->text('pickup_phone')->nullable()->change();
        });
    }
};
