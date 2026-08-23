<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'feature_key',
        'is_enabled',
        'limit_value',
        'metadata',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'metadata' => 'array',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Feature display names
     */
    public static function getDisplayName(string $featureKey): string
    {
        $names = [
            'live_gps' => 'Live GPS Tracking',
            'custom_reports' => 'Custom Reports & Exports',
            'photo_verification' => 'Photo Verification',
            'multi_location' => 'Multi-Location Management',
            'api_access' => 'API Access',
            'push_notifications' => 'Push Notifications',
            'barcode_scanning' => 'Barcode Scanning',
            'temperature_tracking' => 'Temperature Tracking',
            'hipaa_audit_logs' => 'HIPAA Audit Logs',
            'white_label' => 'White Label Branding',
            'sso' => 'Single Sign-On (SSO)',
            'priority_support' => 'Priority Support',
            'bio_hazard_tracking' => 'Bio-hazard Tracking',
            'automated_compliance' => 'Automated Compliance Reports',
        ];

        return $names[$featureKey] ?? ucfirst(str_replace('_', ' ', $featureKey));
    }
}
