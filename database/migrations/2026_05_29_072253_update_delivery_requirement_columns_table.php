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

            // Rename existing columns
            $table->renameColumn(
                'requires_barcode_scan',
                'requires_pickup_barcode_scan'
            );

            $table->renameColumn(
                'requires_signature_or_photo',
                'requires_pickup_signature'
            );

            // Add new columns
            $table->boolean('requires_pickup_photo')
                ->default(0)
                ->after('requires_pickup_signature');

            $table->boolean('requires_dropoff_barcode_scan')
                ->default(0)
                ->after('requires_pickup_photo');

            $table->boolean('requires_dropoff_signature')
                ->default(0)
                ->after('requires_dropoff_barcode_scan');

            $table->boolean('requires_dropoff_photo')
                ->default(0)
                ->after('requires_dropoff_signature');

            $table->boolean('requires_recepient_id_scan')
                ->default(0)
                ->after('requires_dropoff_photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {

            // Drop newly added columns
            $table->dropColumn([
                'requires_pickup_photo',
                'requires_dropoff_barcode_scan',
                'requires_dropoff_signature',
                'requires_dropoff_photo',
                'requires_recepient_id_scan',
            ]);

            // Rename columns back
            $table->renameColumn(
                'requires_pickup_barcode_scan',
                'requires_barcode_scan'
            );

            $table->renameColumn(
                'requires_pickup_signature',
                'requires_signature_or_photo'
            );
        });
    }
};
