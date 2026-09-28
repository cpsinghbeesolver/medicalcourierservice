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
            $table->text('delivery_photo_proof')->nullable()->after('photo_proof');
            $table->longtext('delivery_signature_image')->nullable()->after('signature_image');
            $table->longtext('delivery_notes')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_items', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_photo_proof',
                'signature_image'
            ]);
        });
    }
};
