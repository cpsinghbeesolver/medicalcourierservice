<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaitlistSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_name',
        'phone',
        'email',
        'message',
        'ip_address',
        'user_agent',
        'status',
        'contacted_at',
        'notes',
        'plan_id'
    ];

    protected $casts = [
        'contacted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope to filter by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get pending submissions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get recent submissions
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Mark as contacted
     */
    public function markAsContacted()
    {
        $this->update([
            'status' => 'contacted',
            'contacted_at' => now(),
        ]);
    }

    /**
     * Mark as converted
     */
    public function markAsConverted()
    {
        $this->update([
            'status' => 'converted',
        ]);
    }

    public function plan()
    {
        return $this->belongsTo(
            SubscriptionPlansReference::class,
            'plan_id'
        );
    }
}
