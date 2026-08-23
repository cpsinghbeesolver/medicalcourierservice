<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * HIPAA Compliant Audit Logger
 *
 * Logs all access and modifications to PHI data as required by HIPAA
 * Maintains comprehensive audit trails per 45 CFR § 164.312(b)
 */
class HipaaAuditLogger
{
    /**
     * Log PHI access event
     *
     * @param string $action
     * @param string $entity
     * @param int|null $entityId
     * @param array $phiFields
     * @param array $additionalData
     * @return void
     */
    public static function logPhiAccess(
        string $action,
        string $entity,
        ?int $entityId = null,
        array $phiFields = [],
        array $additionalData = []
    ): void {
        $user = Auth::user();
        $tenantId = $user?->tenant_id ?? session('tenant_id');

        ActivityLog::create([
            'tenant_id' => $tenantId,
            'user_id' => $user?->id,
            'action' => $action,
            'entity_type' => $entity,
            'entity_id' => $entityId,
            'description' => self::buildDescription($action, $entity, $phiFields),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'metadata' => array_merge([
                'phi_accessed' => $phiFields,
                'timestamp' => now()->toIso8601String(),
                'user_role' => $user?->role,
            ], $additionalData),
        ]);
    }

    /**
     * Log data creation event
     *
     * @param string $entity
     * @param int $entityId
     * @param array $phiFields
     * @return void
     */
    public static function logCreated(string $entity, int $entityId, array $phiFields = []): void
    {
        self::logPhiAccess('created', $entity, $entityId, $phiFields);
    }

    /**
     * Log data view/read event
     *
     * @param string $entity
     * @param int $entityId
     * @param array $phiFields
     * @return void
     */
    public static function logViewed(string $entity, int $entityId, array $phiFields = []): void
    {
        self::logPhiAccess('viewed', $entity, $entityId, $phiFields);
    }

    /**
     * Log data update event
     *
     * @param string $entity
     * @param int $entityId
     * @param array $changedFields
     * @param array $oldValues
     * @param array $newValues
     * @return void
     */
    public static function logUpdated(
        string $entity,
        int $entityId,
        array $changedFields,
        array $oldValues = [],
        array $newValues = []
    ): void {
        self::logPhiAccess('updated', $entity, $entityId, $changedFields, [
            'changed_fields' => $changedFields,
            'old_values' => self::maskSensitiveData($oldValues),
            'new_values' => self::maskSensitiveData($newValues),
        ]);
    }

    /**
     * Log data deletion event
     *
     * @param string $entity
     * @param int $entityId
     * @param array $phiFields
     * @return void
     */
    public static function logDeleted(string $entity, int $entityId, array $phiFields = []): void
    {
        self::logPhiAccess('deleted', $entity, $entityId, $phiFields);
    }

    /**
     * Log export event (critical for HIPAA compliance)
     *
     * @param string $entity
     * @param array $exportedIds
     * @param string $format
     * @return void
     */
    public static function logExported(string $entity, array $exportedIds, string $format = 'json'): void
    {
        self::logPhiAccess('exported', $entity, null, [], [
            'exported_ids' => $exportedIds,
            'export_format' => $format,
            'record_count' => count($exportedIds),
        ]);
    }

    /**
     * Log authentication events
     *
     * @param string $action
     * @param int|null $userId
     * @param bool $success
     * @param array $additionalData
     * @return void
     */
    public static function logAuthEvent(
        string $action,
        ?int $userId = null,
        bool $success = true,
        array $additionalData = []
    ): void {
        $tenantId = session('tenant_id');

        ActivityLog::create([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => 'authentication',
            'entity_id' => $userId,
            'description' => "$action " . ($success ? 'successful' : 'failed'),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'metadata' => array_merge([
                'success' => $success,
                'timestamp' => now()->toIso8601String(),
            ], $additionalData),
        ]);
    }

    /**
     * Log unauthorized access attempts
     *
     * @param string $entity
     * @param int|null $entityId
     * @param string $reason
     * @return void
     */
    public static function logUnauthorizedAccess(string $entity, ?int $entityId, string $reason): void
    {
        self::logPhiAccess('unauthorized_access_attempt', $entity, $entityId, [], [
            'reason' => $reason,
            'severity' => 'HIGH',
        ]);
    }

    /**
     * Build human-readable description
     *
     * @param string $action
     * @param string $entity
     * @param array $phiFields
     * @return string
     */
    private static function buildDescription(string $action, string $entity, array $phiFields): string
    {
        $description = ucfirst($action) . ' ' . $entity;

        if (!empty($phiFields)) {
            $description .= ' (PHI fields: ' . implode(', ', $phiFields) . ')';
        }

        return $description;
    }

    /**
     * Mask sensitive data in audit logs
     *
     * @param array $data
     * @return array
     */
    private static function maskSensitiveData(array $data): array
    {
        $sensitiveFields = [
            'password',
            'password_confirmation',
            'token',
            'api_key',
            'secret',
        ];

        foreach ($data as $key => $value) {
            if (in_array($key, $sensitiveFields)) {
                $data[$key] = '***MASKED***';
            } elseif (is_array($value)) {
                $data[$key] = self::maskSensitiveData($value);
            }
        }

        return $data;
    }
}
