<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanCountryPrice extends Model
{
    use HasFactory;
    protected $table= 'plan_country_prices';

    protected $fillable = [
        'plan_id',
        'country_id',
        'currency',
        'price_currency',
        'price_sar',
    ];

    // العلاقة مع خطة الاشتراك
    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    // العلاقة مع الدولة
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
