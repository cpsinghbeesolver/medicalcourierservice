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
        Schema::create('hospital_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('hospital_id')
                ->constrained('hospitals')
                ->cascadeOnDelete();

            $table->text('message')->nullable();

            $table->string('status')->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospital_requests');
    }
};
