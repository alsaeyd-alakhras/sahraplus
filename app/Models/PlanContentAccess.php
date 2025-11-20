<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanContentAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'content_type',
        'content_id',
        'access_type',
    ];

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }
}

