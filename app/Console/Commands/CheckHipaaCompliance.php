<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\ActivityLog;

class CheckHipaaCompliance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hipaa:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check HIPAA compliance status and configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== HIPAA Compliance Status Check ===');
        $this->newLine();

        $issues = [];
        $warnings = [];
        $passed = [];

        // Check 1: HIPAA compliance enabled
        if (config('hipaa.enabled', true)) {
            $passed[] = '✓ HIPAA compliance mode enabled';
        } else {
            $warnings[] = '⚠ HIPAA compliance mode is DISABLED';
        }

        // Check 2: Application key set
        if (config('app.key')) {
            $passed[] = '✓ Application encryption key is set';
        } else {
            $issues[] = '✗ CRITICAL: Application encryption key (APP_KEY) is not set';
        }

        // Check 3: Database encryption
        if (config('hipaa.encryption.enabled', true)) {
            $passed[] = '✓ Database field encryption enabled';
        } else {
            $issues[] = '✗ Database field encryption is disabled';
        }

        // Check 4: Audit logging
        if (config('hipaa.audit.log_all_phi_access', true)) {
            $passed[] = '✓ PHI access logging enabled';
        } else {
            $warnings[] = '⚠ PHI access logging is disabled';
        }

        // Check 5: Session timeout
        $sessionTimeout = config('hipaa.access_control.session_timeout', 15);
        if ($sessionTimeout <= 15) {
            $passed[] = "✓ Session timeout configured ({$sessionTimeout} minutes)";
        } else {
            $warnings[] = "⚠ Session timeout ({$sessionTimeout} min) exceeds recommended 15 minutes";
        }

        // Check 6: SSL/TLS for database
        if (config('hipaa.database.require_ssl', true)) {
            $passed[] = '✓ SSL/TLS required for database connections';
        } else {
            $warnings[] = '⚠ SSL/TLS not required for database connections';
        }

        // Check 7: Backup encryption
        if (config('hipaa.database.backup_encryption_enabled', true)) {
            $passed[] = '✓ Backup encryption enabled';
        } else {
            $warnings[] = '⚠ Backup encryption is disabled';
        }

        // Check 8: Audit log retention
        $retentionDays = config('hipaa.retention.audit_logs_days', 2190);
        if ($retentionDays >= 2190) {
            $passed[] = "✓ Audit log retention configured ({$retentionDays} days / 6 years)";
        } else {
            $warnings[] = "⚠ Audit log retention ({$retentionDays} days) is less than HIPAA requirement (6 years)";
        }

        // Check 9: Password requirements
        $minLength = config('hipaa.password_requirements.min_length', 12);
        if ($minLength >= 12) {
            $passed[] = "✓ Strong password requirements configured (min {$minLength} chars)";
        } else {
            $warnings[] = "⚠ Password minimum length ({$minLength}) is less than recommended (12)";
        }

        // Check 10: Audit logs table exists
        try {
            $auditLogCount = ActivityLog::count();
            $passed[] = "✓ Audit logs table exists ({$auditLogCount} records)";
        } catch (\Exception $e) {
            $issues[] = '✗ Audit logs table not found or not accessible';
        }

        // Check 11: Breach notification
        if (config('hipaa.breach_notification.enabled', true)) {
            $email = config('hipaa.breach_notification.notification_email');
            if ($email && $email !== 'compliance@example.com') {
                $passed[] = "✓ Breach notification configured ({$email})";
            } else {
                $warnings[] = '⚠ Breach notification email not configured or using default';
            }
        } else {
            $warnings[] = '⚠ Breach notification is disabled';
        }

        // Check 12: HTTPS in production
        if (app()->environment('production')) {
            if (config('app.url') && str_starts_with(config('app.url'), 'https://')) {
                $passed[] = '✓ HTTPS configured for production';
            } else {
                $issues[] = '✗ CRITICAL: HTTPS not configured in production';
            }
        } else {
            $passed[] = '✓ Running in non-production environment';
        }

        // Display results
        $this->newLine();
        $this->info('=== Checks Passed ===');
        foreach ($passed as $pass) {
            $this->line("<fg=green>{$pass}</>");
        }

        if (!empty($warnings)) {
            $this->newLine();
            $this->warn('=== Warnings ===');
            foreach ($warnings as $warning) {
                $this->line("<fg=yellow>{$warning}</>");
            }
        }

        if (!empty($issues)) {
            $this->newLine();
            $this->error('=== Critical Issues ===');
            foreach ($issues as $issue) {
                $this->line("<fg=red>{$issue}</>");
            }
        }

        // Overall status
        $this->newLine();
        if (empty($issues)) {
            if (empty($warnings)) {
                $this->info('✓✓✓ All HIPAA compliance checks passed! ✓✓✓');
                return 0;
            } else {
                $this->warn('⚠ HIPAA compliance checks passed with warnings. Review warnings above.');
                return 0;
            }
        } else {
            $this->error('✗✗✗ CRITICAL: HIPAA compliance issues detected! ✗✗✗');
            $this->error('Please address the critical issues above before deploying to production.');
            return 1;
        }
    }
}
