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
        Schema::create('company_hospitals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('hospital_id')
                ->constrained('hospitals')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique([
                'company_id',
                'hospital_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_hospitals');
    }
};
