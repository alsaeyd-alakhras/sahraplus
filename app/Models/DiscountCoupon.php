<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscountCoupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name_ar', 'name_en', 'description_ar', 'description_en',
        'discount_type', 'discount_value', 'min_amount', 'max_discount',
        'usage_limit', 'usage_limit_per_user', 'used_count', 'starts_at',
        'expires_at', 'applicable_plans', 'first_time_only', 'is_active', 'created_by'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_limit_per_user' => 'integer',
        'used_count' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'applicable_plans' => 'array',
        'first_time_only' => 'boolean',
        'is_active' => 'boolean',
    ];

    // العلاقات
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usages()
    {
        return $this->hasMany(CouponUsage::class, 'coupon_id');
    }

    // Accessors
    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getIsValidAttribute()
    {
        return $this->is_active
            && $this->starts_at <= now()
            && $this->expires_at >= now()
            && ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }

    public function getDiscountTextAttribute()
    {
        switch ($this->discount_type) {
            case 'percentage':
                return $this->discount_value . '%';
            case 'fixed':
                return  number_format($this->discount_value, 2);
            case 'free_trial':
                return $this->discount_value . ' days free';
            default:
                return $this->discount_value;
        }
    }

    // Methods
    public function isValidForUser($userId, $planId = null, $amount = null)
    {
        if (!$this->is_valid) return false;

        if ($amount && $amount < $this->min_amount) return false;

        if ($planId && $this->applicable_plans && !in_array($planId, $this->applicable_plans)) {
            return false;
        }

        if ($this->first_time_only) {
            $hasSubscription = UserSubscription::where('user_id', $userId)->exists();
            if ($hasSubscription) return false;
        }

        $userUsageCount = $this->usages()->where('user_id', $userId)->count();
        if ($userUsageCount >= $this->usage_limit_per_user) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($amount)
    {
        switch ($this->discount_type) {
            case 'percentage':
                $discount = $amount * ($this->discount_value / 100);
                break;
            case 'fixed':
                $discount = $this->discount_value;
                break;
            case 'free_trial':
                $discount = $amount;
                break;
            default:
                $discount = 0;
        }

        if ($this->max_discount && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }

        return min($discount, $amount);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->active()
                    ->where('starts_at', '<=', now())
                    ->where('expires_at', '>=', now())
                    ->where(function($q) {
                        $q->whereNull('usage_limit')
                          ->orWhereRaw('used_count < usage_limit');
                    });
    }
}
