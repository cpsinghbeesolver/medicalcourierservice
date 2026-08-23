<?php

return [
    /*
    |--------------------------------------------------------------------------
    | HIPAA Compliance Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for HIPAA compliance including data retention,
    | audit logging, and PHI field definitions
    |
    */

    /**
     * Enable HIPAA compliance mode
     * When enabled, all PHI fields will be encrypted and all access will be logged
     */
    'enabled' => env('HIPAA_COMPLIANCE_ENABLED', true),

    /**
     * Data retention period (in days)
     * HIPAA requires keeping audit logs for at least 6 years
     */
    'retention' => [
        'audit_logs_days' => env('HIPAA_AUDIT_LOG_RETENTION_DAYS', 2190), // 6 years
        'delivery_records_days' => env('HIPAA_DELIVERY_RETENTION_DAYS', 2190), // 6 years
        'user_data_days' => env('HIPAA_USER_DATA_RETENTION_DAYS', 2190), // 6 years
    ],

    /**
     * PHI fields by model
     * Define which fields contain Protected Health Information
     */
    'phi_fields' => [
        'delivery' => [
            'patient_initials',
            'specimen_id',
            'pickup_name',
            'pickup_address',
            'pickup_city',
            'pickup_state',
            'pickup_zip',
            'pickup_phone',
            'pickup_contact_person',
            'delivery_name',
            'delivery_address',
            'delivery_city',
            'delivery_state',
            'delivery_zip',
            'delivery_phone',
            'delivery_contact_person',
            'special_instructions',
            'notes',
        ],
        'delivery_item' => [
            'barcode',
            'specimen_type',
            'description',
        ],
        'user' => [
            'name',
            'email',
            'phone',
        ],
        'driver_profile' => [
            'license_number',
            'license_state',
            'emergency_contact_name',
            'emergency_contact_phone',
            'hipaa_certification_file',
        ],
    ],

    /**
     * Audit log settings
     */
    'audit' => [
        'log_all_phi_access' => env('HIPAA_LOG_ALL_PHI_ACCESS', true),
        'log_authentication' => env('HIPAA_LOG_AUTHENTICATION', true),
        'log_unauthorized_attempts' => env('HIPAA_LOG_UNAUTHORIZED', true),
        'alert_on_suspicious_activity' => env('HIPAA_ALERT_SUSPICIOUS', true),
    ],

    /**
     * Encryption settings
     */
    'encryption' => [
        'algorithm' => 'AES-256-CBC',
        'enabled' => env('HIPAA_ENCRYPTION_ENABLED', true),

        // Fields that should always be encrypted
        'always_encrypt' => [
            'patient_initials',
            'specimen_id',
        ],
    ],

    /**
     * Access control settings
     */
    'access_control' => [
        // Role-based access to PHI
        'roles' => [
            'admin' => [
                'can_view_all_phi' => true,
                'can_export_phi' => true,
                'can_delete_phi' => true,
            ],
            'coordinator' => [
                'can_view_all_phi' => true,
                'can_export_phi' => false,
                'can_delete_phi' => false,
            ],
            'driver' => [
                'can_view_all_phi' => false,
                'can_export_phi' => false,
                'can_delete_phi' => false,
            ],
        ],

        // Session timeout (in minutes) - HIPAA recommends auto-logout
        'session_timeout' => env('HIPAA_SESSION_TIMEOUT', 15),

        // Require multi-factor authentication
        'require_mfa' => env('HIPAA_REQUIRE_MFA', false),
    ],

    /**
     * Breach notification settings
     */
    'breach_notification' => [
        'enabled' => env('HIPAA_BREACH_NOTIFICATION_ENABLED', true),
        'notification_email' => env('HIPAA_BREACH_NOTIFICATION_EMAIL', 'compliance@example.com'),

        // Thresholds for breach detection
        'thresholds' => [
            'failed_login_attempts' => 5,
            'unauthorized_access_attempts' => 3,
        ],
    ],

    /**
     * De-identification settings
     * For data that needs to be used without PHI
     */
    'deidentification' => [
        'enabled' => env('HIPAA_DEIDENTIFICATION_ENABLED', true),

        // Methods for de-identification
        'methods' => [
            'patient_initials' => 'hash', // Replace with hash
            'addresses' => 'partial', // Keep only city/state
            'phone' => 'mask', // Mask digits
        ],
    ],

    /**
     * Database encryption at rest
     */
    'database' => [
        // Enable transparent data encryption (TDE) if supported by database
        'tde_enabled' => env('DB_TDE_ENABLED', false),

        // Backup encryption
        'backup_encryption_enabled' => env('DB_BACKUP_ENCRYPTION_ENABLED', true),

        // Require SSL/TLS for database connections
        'require_ssl' => env('DB_REQUIRE_SSL', true),
    ],

    /**
     * Minimum password requirements for HIPAA compliance
     */
    'password_requirements' => [
        'min_length' => 12,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_special_chars' => true,
        'expiry_days' => 90,
        'prevent_reuse' => 5, // Number of previous passwords to prevent reuse
    ],
];
