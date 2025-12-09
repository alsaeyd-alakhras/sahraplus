<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanLimitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'limitation_type',
        'limitation_key',
        'limitation_value',
        'limitation_unit',
        'description_ar',
        'description_en',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }
}

