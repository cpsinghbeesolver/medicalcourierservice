<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use App\Traits\BelongsToTenant;
use App\Traits\EncryptsPhiData;
use Illuminate\Support\Str;
use App\Models\Notifications;
use App\Models\DeliveryItem;
use App\Models\Delivery;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    //use HasFactory, Notifiable, HasApiTokens, BelongsToTenant, EncryptsPhiData;
    use HasFactory, Notifiable, HasApiTokens,EncryptsPhiData;
    /**
     * PHI fields that should be encrypted at rest (HIPAA compliance)
     */
    protected $encryptedPhiFields = [
        'name',
        'phone',
        'address',
    ];

    //protected $with = ['roleRelation'];
    protected $appends = ['role'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'verification_code',
        'verification_code_expires_at',
        'role_id',
        'phone',
        'dob',
        'address',
        'profile_photo',
        'status',
        'last_login_at',
        'device_token',
        'device_type',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'roleRelation'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'verification_code_expires_at' => 'datetime',
            'dob' => 'date',
        ];
    }
    
    public function roleRelation()
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }
    
    public function getRoleAttribute()
    {
        return Str::lower($this->roleRelation?->name);
    }

    /**
     * Get the driver profile associated with the user.
     */
    public function driverProfile(): HasOne
    {
        return $this->hasOne(DriverProfile::class);
    }

    /**
     * Get the deliveries assigned to this driver.
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'driver_id');
    }

    /**
     * Get deliveries created by this user.
     */
    public function createdDeliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'created_by');
    }

    /**
     * Get the activity logs for this user.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get the subscription for this user.
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    /**
     * Check if user is a driver.
     */
    public function isDriver(): bool
    {
        return $this->role_id == '4';
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role_id == '1';
    }

    /**
     * Check if user is a dispatcher.
     */
    public function isDispatcher(): bool
    {
        return $this->role_id == '2';
    }

    /**
     * Check if user is a dispatcher.
     */
    public function isHospital(): bool
    {
        return $this->role_id == '3';
    }

    //Get Notifications
    public function notifications()
    {
        return $this->hasMany(Notifications::class);
    }

    public function hasUnreadNotification(): bool
    {
        return $this->notifications()
            ->where('is_read', 0)
            ->exists();
    }
    
    public function hospital(): HasOne
    {
        return $this->hasOne(Hospital::class, 'hospital_id', 'id');
    }
}
