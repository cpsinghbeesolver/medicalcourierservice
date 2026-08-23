<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * HIPAA requires efficient audit log querying for compliance reporting
     */
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            // Add tenant_id column if it doesn't exist
            if (!Schema::hasColumn('activity_logs', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            }
        });

        // Add indexes with try-catch to avoid duplicate errors
        $this->addIndexSafely('activity_logs', ['user_id', 'created_at'], 'idx_activity_logs_user_date');
        $this->addIndexSafely('activity_logs', ['model_type', 'model_id', 'created_at'], 'idx_activity_logs_model_date');
        $this->addIndexSafely('activity_logs', ['action', 'created_at'], 'idx_activity_logs_action_date');
        $this->addIndexSafely('activity_logs', ['tenant_id', 'created_at'], 'idx_activity_logs_tenant');
        $this->addIndexSafely('activity_logs', ['tenant_id', 'model_type', 'action', 'created_at'], 'idx_activity_logs_phi_access');
        $this->addIndexSafely('activity_logs', 'ip_address', 'idx_activity_logs_ip');
    }

    /**
     * Safely add an index, ignoring errors if it already exists
     */
    private function addIndexSafely(string $table, $columns, string $indexName): void
    {
        try {
            Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
                $table->index($columns, $indexName);
            });
        } catch (\Exception $e) {
            // Index might already exist, ignore
            if (!str_contains($e->getMessage(), 'Duplicate key name')) {
                throw $e;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes safely
        $this->dropIndexSafely('activity_logs', 'idx_activity_logs_user_date');
        $this->dropIndexSafely('activity_logs', 'idx_activity_logs_model_date');
        $this->dropIndexSafely('activity_logs', 'idx_activity_logs_action_date');
        $this->dropIndexSafely('activity_logs', 'idx_activity_logs_tenant');
        $this->dropIndexSafely('activity_logs', 'idx_activity_logs_phi_access');
        $this->dropIndexSafely('activity_logs', 'idx_activity_logs_ip');

        Schema::table('activity_logs', function (Blueprint $table) {
            // Drop tenant_id column if it exists
            if (Schema::hasColumn('activity_logs', 'tenant_id')) {
                $table->dropForeign(['tenant_id']);
                $table->dropColumn('tenant_id');
            }
        });
    }

    /**
     * Safely drop an index, ignoring errors if it doesn't exist
     */
    private function dropIndexSafely(string $table, string $indexName): void
    {
        try {
            Schema::table($table, function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        } catch (\Exception $e) {
            // Index might not exist, ignore
        }
    }
};
