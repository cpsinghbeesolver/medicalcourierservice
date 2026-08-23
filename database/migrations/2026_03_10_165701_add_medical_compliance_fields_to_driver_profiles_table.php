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
        Schema::table('driver_profiles', function (Blueprint $table) {
            $table->string('license_state')->nullable()->after('license_expiry_date');
            $table->date('date_of_birth')->nullable()->after('zip_code');
            $table->string('insurance_policy_number')->nullable()->after('date_of_birth');
            $table->date('insurance_expiry_date')->nullable()->after('insurance_policy_number');
            $table->date('hipaa_certification_date')->nullable()->after('insurance_expiry_date');
            $table->string('background_check_status')->nullable()->after('hipaa_certification_date');
            $table->date('drug_screen_expiry')->nullable()->after('background_check_status');
            $table->date('specimen_handling_certification_date')->nullable()->after('drug_screen_expiry');
            $table->boolean('specimen_handling_confirmed')->default(false)->after('specimen_handling_certification_date');
            $table->date('bloodborne_pathogen_training_date')->nullable()->after('specimen_handling_confirmed');
            $table->string('bloodborne_pathogen_file')->nullable()->after('bloodborne_pathogen_training_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'license_state',
                'date_of_birth',
                'insurance_policy_number',
                'insurance_expiry_date',
                'hipaa_certification_date',
                'background_check_status',
                'drug_screen_expiry',
                'specimen_handling_certification_date',
                'specimen_handling_confirmed',
                'bloodborne_pathogen_training_date',
                'bloodborne_pathogen_file',
            ]);
        });
    }
};
