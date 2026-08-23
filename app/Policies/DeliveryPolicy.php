<?php

namespace App\Policies;

use App\Models\Delivery;
use App\Models\User;
use App\Services\HipaaAuditLogger;

/**
 * Delivery Policy - HIPAA Compliant Access Control
 *
 * Implements role-based access control for PHI data
 */
class DeliveryPolicy
{
    /**
     * Determine if the user can view any deliveries.
     */
    public function viewAny(User $user): bool
    {
        // Admins and coordinators can view all deliveries
        return in_array($user->role, ['admin', 'coordinator']);
    }

    /**
     * Determine if the user can view the delivery.
     */
    public function view(User $user, Delivery $delivery): bool
    {
        // Admins and coordinators can view any delivery
        if (in_array($user->role, ['admin', 'coordinator'])) {
            return true;
        }

        // Drivers can only view their assigned deliveries
        if ($user->role === 'driver') {
            return $delivery->driver_id === $user->id;
        }

        // Log unauthorized attempt
        HipaaAuditLogger::logUnauthorizedAccess(
            'delivery',
            $delivery->id,
            'User does not have permission to view this delivery'
        );

        return false;
    }

    /**
     * Determine if the user can create deliveries.
     */
    public function create(User $user): bool
    {
        // Only admins and coordinators can create deliveries
        return in_array($user->role, ['admin', 'coordinator']);
    }

    /**
     * Determine if the user can update the delivery.
     */
    public function update(User $user, Delivery $delivery): bool
    {
        // Admins can update any delivery
        if ($user->role === 'admin') {
            return true;
        }

        // Coordinators can update deliveries in their tenant
        if ($user->role === 'coordinator' && $delivery->tenant_id === $user->tenant_id) {
            return true;
        }

        // Drivers can update status of their assigned deliveries
        if ($user->role === 'driver' && $delivery->driver_id === $user->id) {
            // Drivers can only update specific fields (status, timestamps, etc.)
            // Additional field-level validation should be done in the controller
            return true;
        }

        // Log unauthorized attempt
        HipaaAuditLogger::logUnauthorizedAccess(
            'delivery',
            $delivery->id,
            'User does not have permission to update this delivery'
        );

        return false;
    }

    /**
     * Determine if the user can delete the delivery.
     */
    public function delete(User $user, Delivery $delivery): bool
    {
        // Only admins can delete deliveries
        $canDelete = $user->role === 'admin';

        if (!$canDelete) {
            HipaaAuditLogger::logUnauthorizedAccess(
                'delivery',
                $delivery->id,
                'User does not have permission to delete this delivery'
            );
        }

        return $canDelete;
    }

    /**
     * Determine if the user can permanently delete the delivery.
     */
    public function forceDelete(User $user, Delivery $delivery): bool
    {
        // Force delete should be extremely restricted
        // Consider requiring additional approval/logging
        $canForceDelete = $user->role === 'admin';

        if ($canForceDelete) {
            // Log this high-risk action
            HipaaAuditLogger::logPhiAccess(
                'force_delete_attempt',
                'delivery',
                $delivery->id,
                ['patient_initials', 'specimen_id'],
                ['severity' => 'CRITICAL', 'requires_review' => true]
            );
        } else {
            HipaaAuditLogger::logUnauthorizedAccess(
                'delivery',
                $delivery->id,
                'User does not have permission to permanently delete this delivery'
            );
        }

        return $canForceDelete;
    }

    /**
     * Determine if the user can export deliveries.
     */
    public function export(User $user): bool
    {
        // Only admins can export PHI data
        $canExport = $user->role === 'admin';

        if (!$canExport) {
            HipaaAuditLogger::logUnauthorizedAccess(
                'delivery',
                null,
                'User does not have permission to export deliveries'
            );
        }

        return $canExport;
    }

    /**
     * Determine if the user can view PHI fields.
     */
    public function viewPhi(User $user, Delivery $delivery): bool
    {
        // Admins and coordinators can view all PHI
        if (in_array($user->role, ['admin', 'coordinator'])) {
            return true;
        }

        // Drivers can view limited PHI for their assigned deliveries
        if ($user->role === 'driver' && $delivery->driver_id === $user->id) {
            // Drivers can see addresses and contact info, but not patient details
            return true;
        }

        return false;
    }
}
