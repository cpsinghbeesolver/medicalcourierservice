<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ActivityLog;
use App\Models\Delivery;
use App\Models\User;
use Carbon\Carbon;

class GenerateHipaaReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hipaa:report
                            {--start= : Start date (YYYY-MM-DD)}
                            {--end= : End date (YYYY-MM-DD)}
                            {--tenant= : Tenant ID}
                            {--user= : User ID}
                            {--entity= : Entity type (delivery, user, etc.)}
                            {--output= : Output format (console, csv, json)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate HIPAA compliance audit report for PHI access';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startDate = $this->option('start') ? Carbon::parse($this->option('start')) : Carbon::now()->subDays(30);
        $endDate = $this->option('end') ? Carbon::parse($this->option('end')) : Carbon::now();
        $output = $this->option('output') ?? 'console';

        $this->info("Generating HIPAA Audit Report");
        $this->info("Period: {$startDate->toDateString()} to {$endDate->toDateString()}");
        $this->newLine();

        $query = ActivityLog::whereBetween('created_at', [$startDate, $endDate]);

        if ($tenantId = $this->option('tenant')) {
            $query->where('tenant_id', $tenantId);
        }

        if ($userId = $this->option('user')) {
            $query->where('user_id', $userId);
        }

        if ($entity = $this->option('entity')) {
            $query->where('entity_type', $entity);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        if ($output === 'console') {
            $this->displayConsoleReport($logs, $startDate, $endDate);
        } elseif ($output === 'csv') {
            $this->generateCsvReport($logs, $startDate, $endDate);
        } elseif ($output === 'json') {
            $this->generateJsonReport($logs, $startDate, $endDate);
        }

        return 0;
    }

    /**
     * Display report in console
     */
    private function displayConsoleReport($logs, $startDate, $endDate)
    {
        // Summary statistics
        $this->info("=== HIPAA Audit Summary ===");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total PHI Access Events', $logs->count()],
                ['Unique Users', $logs->pluck('user_id')->unique()->count()],
                ['Unique Entities Accessed', $logs->pluck('entity_id')->unique()->count()],
                ['View Operations', $logs->where('action', 'viewed')->count()],
                ['Create Operations', $logs->where('action', 'created')->count()],
                ['Update Operations', $logs->where('action', 'updated')->count()],
                ['Delete Operations', $logs->where('action', 'deleted')->count()],
                ['Unauthorized Attempts', $logs->where('action', 'unauthorized_access_attempt')->count()],
            ]
        );

        $this->newLine();

        // Access by user
        $this->info("=== Access by User ===");
        $byUser = $logs->groupBy('user_id')->map(function ($userLogs) {
            $user = User::find($userLogs->first()->user_id);
            return [
                'User' => $user ? "{$user->name} ({$user->email})" : 'Unknown',
                'Role' => $user?->role ?? 'N/A',
                'Access Count' => $userLogs->count(),
            ];
        })->sortByDesc('Access Count')->take(10);

        $this->table(['User', 'Role', 'Access Count'], $byUser->values()->toArray());

        $this->newLine();

        // Recent PHI access
        $this->info("=== Recent PHI Access (Last 20) ===");
        $recentAccess = $logs->take(20)->map(function ($log) {
            $user = User::find($log->user_id);
            return [
                'Date/Time' => $log->created_at->format('Y-m-d H:i:s'),
                'User' => $user?->name ?? 'Unknown',
                'Action' => $log->action,
                'Entity' => $log->entity_type . ' #' . $log->entity_id,
                'IP Address' => $log->ip_address,
            ];
        });

        $this->table(['Date/Time', 'User', 'Action', 'Entity', 'IP Address'], $recentAccess->toArray());

        $this->newLine();
        $this->info("Report generated successfully!");
    }

    /**
     * Generate CSV report
     */
    private function generateCsvReport($logs, $startDate, $endDate)
    {
        $filename = storage_path('app/hipaa_audit_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.csv');

        $handle = fopen($filename, 'w');
        fputcsv($handle, ['Timestamp', 'User ID', 'User Name', 'User Email', 'Role', 'Action', 'Entity Type', 'Entity ID', 'IP Address', 'Description']);

        foreach ($logs as $log) {
            $user = User::find($log->user_id);
            fputcsv($handle, [
                $log->created_at->toIso8601String(),
                $log->user_id,
                $user?->name ?? 'Unknown',
                $user?->email ?? 'Unknown',
                $user?->role ?? 'Unknown',
                $log->action,
                $log->entity_type,
                $log->entity_id,
                $log->ip_address,
                $log->description,
            ]);
        }

        fclose($handle);

        $this->info("CSV report generated: {$filename}");
    }

    /**
     * Generate JSON report
     */
    private function generateJsonReport($logs, $startDate, $endDate)
    {
        $filename = storage_path('app/hipaa_audit_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.json');

        $data = [
            'report_generated_at' => now()->toIso8601String(),
            'period' => [
                'start' => $startDate->toIso8601String(),
                'end' => $endDate->toIso8601String(),
            ],
            'summary' => [
                'total_events' => $logs->count(),
                'unique_users' => $logs->pluck('user_id')->unique()->count(),
                'by_action' => $logs->groupBy('action')->map->count()->toArray(),
            ],
            'events' => $logs->map(function ($log) {
                $user = User::find($log->user_id);
                return [
                    'timestamp' => $log->created_at->toIso8601String(),
                    'user' => [
                        'id' => $log->user_id,
                        'name' => $user?->name,
                        'email' => $user?->email,
                        'role' => $user?->role,
                    ],
                    'action' => $log->action,
                    'entity' => [
                        'type' => $log->entity_type,
                        'id' => $log->entity_id,
                    ],
                    'ip_address' => $log->ip_address,
                    'description' => $log->description,
                    'metadata' => $log->metadata,
                ];
            })->toArray(),
        ];

        file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));

        $this->info("JSON report generated: {$filename}");
    }
}
