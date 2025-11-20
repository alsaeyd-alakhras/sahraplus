<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'starts_at',
        'expires_at',
        'usage_limit',
        'usage_limit_per_user',
        'times_used',
        'is_active',
        'plan_id',
        'metadata',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'usage_limit' => 'integer',
        'usage_limit_per_user' => 'integer',
        'times_used' => 'integer',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function redemptions()
    {
        return $this->hasMany(CouponRedemption::class);
    }

    /**
     * Check if coupon is valid
     */
    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($this->starts_at && $now->isBefore($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && $now->isAfter($this->expires_at)) {
            return false;
        }

        if ($this->usage_limit && $this->times_used >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if user can use this coupon
     */
    public function canBeUsedByUser($userId)
    {
        if (!$this->isValid()) {
            return false;
        }

        $userUsageCount = $this->redemptions()
            ->where('user_id', $userId)
            ->count();

        return $userUsageCount < $this->usage_limit_per_user;
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount($amount)
    {
        if ($this->discount_type === 'fixed') {
            return min($this->discount_value, $amount);
        }

        // percentage
        return ($amount * $this->discount_value) / 100;
    }
}

