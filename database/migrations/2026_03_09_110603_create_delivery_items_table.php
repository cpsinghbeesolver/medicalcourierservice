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
        Schema::create('delivery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained()->onDelete('cascade');
            $table->string('item_type'); // specimen, document, package, container
            $table->string('item_code')->nullable();
            $table->string('specimen_type')->nullable(); // blood, urine, tissue, etc.
            $table->string('barcode')->nullable();
            $table->integer('quantity')->default(1);
            $table->text('description')->nullable();
            $table->enum('temperature_requirement', ['ambient', 'refrigerated', 'frozen', 'dry_ice', 'controlled'])->nullable();
            $table->boolean('requires_special_handling')->default(false);
            $table->text('handling_instructions')->nullable();
            $table->enum('status', ['pending', 'collected', 'in_transit', 'delivered', 'damaged'])->default('pending');
            $table->timestamps();

            $table->index('delivery_id');
            $table->index('barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_items');
    }
};
