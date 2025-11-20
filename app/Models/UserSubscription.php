<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'starts_at' => 'datetime',
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

    public function canAccessCategory($categoryId)
    {
        return !$this->plan->contentAccess()
            ->where('content_type', 'category')
            ->where('content_id', $categoryId)
            ->where('access_type', 'deny')
            ->exists();
    }

    public function canAccessMovie($movieId)
    {
        return !$this->plan->contentAccess()
            ->where('content_type', 'movie')
            ->where('content_id', $movieId)
            ->where('access_type', 'deny')
            ->exists();
    }

    public function canAccessSeries($seriesId)
    {
        return !$this->plan->contentAccess()
            ->where('content_type', 'series')
            ->where('content_id', $seriesId)
            ->where('access_type', 'deny')
            ->exists();
    }

    public function canUseQuality($quality)
    {
        $allowed = $this->plan->limitations()->where('limitation_key', 'allowed_qualities')->first();
        if (!$allowed) {
            return true;
        }

        return in_array($quality, explode(',', $allowed->limitation_value));
    }

    public function canDownload()
    {
        return $this->plan->download_enabled;
    }
}

