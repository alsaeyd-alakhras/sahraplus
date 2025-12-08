<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'amount',
        'currency',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'auto_renew',
        'payment_method',
        'external_subscription_id',
        'metadata',
        'canceled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'auto_renew' => 'boolean',
        'metadata' => 'array',
        'canceled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function payments()
    {
        return $this->hasMany(Payments::class, 'subscription_id');
    }

    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class, 'subscription_id');
    }

    // Accessors
    public function getDaysRemainingAttribute()
    {
        if (!$this->ends_at) return null;
        return max(0, Carbon::now()->diffInDays($this->ends_at, false));
    }

    public function getIsActiveAttribute()
    {
        return in_array($this->status, ['active', 'trial']) && $this->ends_at > now();
    }

    public function getIsTrialAttribute()
    {
        return $this->status === 'trial' && $this->trial_ends_at && $this->trial_ends_at > now();
    }
    public function getStatusTextAttribute()
    {
        $statuses = [
            'trial' => app()->getLocale() === 'ar' ? 'تجربة مجانية' : 'Trial',
            'active' => app()->getLocale() === 'ar' ? 'نشط' : 'Active',
            'canceled' => app()->getLocale() === 'ar' ? 'ملغي' : 'Canceled',
            'expired' => app()->getLocale() === 'ar' ? 'منتهي' : 'Expired',
            'suspended' => app()->getLocale() === 'ar' ? 'معلق' : 'Suspended',
            'pending' => app()->getLocale() === 'ar' ? 'معلق' : 'Pending',
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    public function canAccessCategory($categoryId)
    {
        return $this->plan->contentAccess()
            ->where('content_type', 'category')
            ->where('content_id', $categoryId)
            ->where('access_type', 'allow')
            ->exists();
    }

    public function canAccessMovie($movieId)
    {
        return $this->plan->contentAccess()
            ->where('content_type', 'movie')
            ->where('content_id', $movieId)
            ->where('access_type', 'allow')
            ->exists();
    }

    public function canAccessSeries($seriesId)
    {
        return $this->plan->contentAccess()
            ->where('content_type', 'series')
            ->where('content_id', $seriesId)
            ->where('access_type', 'allow')
            ->exists();
    }

    // public function canUseQuality($quality)
    // {
    //     $allowed = $this->plan->limitations()->where('limitation_key', 'allowed_qualities')->first();
    //     if (!$allowed) {
    //         return true;
    //     }

    //     return in_array($quality, explode(',', $allowed->limitation_value));
    // }

    public function canDownload()
    {
        return $this->plan->download_enabled;
    }

    public function canUseQuality($quality)
    {
        $maxQuality = $this->plan->video_quality; // sd / hd / uhd
        $qualities = ['sd' => 1, 'hd' => 2, 'uhd' => 3];
        return $qualities[$quality] <= $qualities[$maxQuality];
    }
}
