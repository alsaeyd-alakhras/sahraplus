<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',

        'price',
        'currency',
        'billing_period',
        'video_quality',

        'trial_days',
        'max_profiles',
        'max_devices',

        'sort_order',
        'slug',

        'download_enabled',
        'ads_enabled',
        'live_tv_enabled',
        'is_popular',
        'is_active',
    ];


    protected $casts = [
        'price' => 'decimal:2',
        'trial_days' => 'integer',
        'max_profiles' => 'integer',
        'max_devices' => 'integer',
        'download_enabled' => 'boolean',
        'ads_enabled' => 'boolean',
        'live_tv_enabled' => 'boolean',
        'features' => 'array',
        'sort_order' => 'integer',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function limitations()
    {
        return $this->hasMany(PlanLimitation::class, 'plan_id');
    }

    public function contentAccess()
    {
        return $this->hasMany(PlanContentAccess::class, 'plan_id');
    }

    public function activeSubscriptions()
    {
        return $this->hasMany(UserSubscription::class, 'plan_id')->whereIn('status', ['active', 'trial']);
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class, 'plan_id');
    }
}