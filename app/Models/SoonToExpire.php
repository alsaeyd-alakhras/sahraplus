<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoonToExpire extends Model
{
    protected $table = 'soon_to_expire';

    // لا يحتاج timestamps لأنه view
    public $timestamps = false;

    // للقراءة فقط
    protected $guarded = ['*'];

    protected $casts = [
        'ends_at' => 'datetime',
        'auto_renew' => 'boolean',
        'days_remaining' => 'integer',
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_id');
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    // Accessors
    public function getPlanNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->plan_name_ar : $this->plan_name_en;
    }

    public function getUrgencyColorAttribute()
    {
        switch ($this->urgency_level) {
            case 'critical':
                return '#dc3545'; // أحمر
            case 'warning':
                return '#fd7e14'; // برتقالي
            case 'notice':
                return '#ffc107'; // أصفر
            default:
                return '#28a745'; // أخضر
        }
    }

    // Scopes
    public function scopeCritical($query)
    {
        return $query->where('urgency_level', 'critical');
    }

    public function scopeWarning($query)
    {
        return $query->where('urgency_level', 'warning');
    }

    public function scopeAutoRenewDisabled($query)
    {
        return $query->where('auto_renew', false);
    }
}
