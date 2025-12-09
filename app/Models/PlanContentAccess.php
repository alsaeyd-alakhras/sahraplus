<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanContentAccess extends Model
{
    use HasFactory;

    protected $table= 'plan_content_access';

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

     public function category()
    {
        return $this->belongsTo(Category::class, 'content_id');
    }
    public function movie()
    {
        return $this->belongsTo(Movie::class, 'content_id');
    }

    public function series()
    {
        return $this->belongsTo(Series::class, 'content_id');
    }
}