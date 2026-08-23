<?php

namespace App\Observers;

use App\Models\Delivery;
use App\Services\HipaaAuditLogger;

/**
 * Delivery Observer for HIPAA Audit Logging
 *
 * Automatically logs all PHI access and modifications
 */
class DeliveryObserver
{
    /**
     * Handle the Delivery "retrieved" event.
     */
    public function retrieved(Delivery $delivery): void
    {
        // Only log if PHI fields are accessed
        if (config('hipaa.audit.log_all_phi_access', true)) {
            HipaaAuditLogger::logViewed('delivery', $delivery->id, $delivery->getEncryptedPhiFields());
        }
    }

    /**
     * Handle the Delivery "created" event.
     */
    public function created(Delivery $delivery): void
    {
        HipaaAuditLogger::logCreated('delivery', $delivery->id, $delivery->getEncryptedPhiFields());
    }

    /**
     * Handle the Delivery "updated" event.
     */
    public function updated(Delivery $delivery): void
    {
        $changedFields = array_keys($delivery->getChanges());
        $phiChanged = array_intersect($changedFields, $delivery->getEncryptedPhiFields());

        if (!empty($phiChanged)) {
            $oldValues = [];
            $newValues = [];

            foreach ($phiChanged as $field) {
                $oldValues[$field] = $delivery->getOriginal($field);
                $newValues[$field] = $delivery->$field;
            }

            HipaaAuditLogger::logUpdated(
                'delivery',
                $delivery->id,
                $phiChanged,
                $oldValues,
                $newValues
            );
        }
    }

    /**
     * Handle the Delivery "deleted" event.
     */
    public function deleted(Delivery $delivery): void
    {
        HipaaAuditLogger::logDeleted('delivery', $delivery->id, $delivery->getEncryptedPhiFields());
    }

    /**
     * Handle the Delivery "force deleted" event.
     */
    public function forceDeleted(Delivery $delivery): void
    {
        HipaaAuditLogger::logPhiAccess(
            'force_deleted',
            'delivery',
            $delivery->id,
            $delivery->getEncryptedPhiFields(),
            ['severity' => 'HIGH']
        );
    }
}
