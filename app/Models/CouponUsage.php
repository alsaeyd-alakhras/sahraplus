<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CouponUsage extends Model
{
    use HasFactory;

    protected $table = 'coupon_usage';

    protected $fillable = [
        'coupon_id',
        'user_id',
        'subscription_id',
        'payment_id',
        'original_amount',
        'discount_amount',
        'final_amount',
        'currency',
        'used_at'
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'used_at' => 'datetime',
    ];

    // العلاقات
    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payments::class, 'payment_id');
    }

    // Accessors
    public function getSavingsPercentageAttribute()
    {
        if ($this->original_amount == 0) return 0;
        return round(($this->discount_amount / $this->original_amount) * 100, 2);
    }

    // Scopes
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('used_at', now()->month)
            ->whereYear('used_at', now()->year);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
