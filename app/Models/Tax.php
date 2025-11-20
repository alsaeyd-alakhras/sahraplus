<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar', 'name_en', 'tax_code', 'tax_type', 'tax_rate',
        'applicable_countries', 'applicable_plans', 'min_amount',
        'max_amount', 'compound_tax', 'sort_order', 'is_active',
        'effective_from', 'effective_until'
    ];

    protected $casts = [
        'tax_rate' => 'decimal:3',
        'applicable_countries' => 'array',
        'applicable_plans' => 'array',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'compound_tax' => 'boolean',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_until' => 'date',
    ];

    // Accessors
    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getIsCurrentAttribute()
    {
        return $this->is_active
            && $this->effective_from <= now()->toDateString()
            && (!$this->effective_until || $this->effective_until >= now()->toDateString());
    }

    public function getFormattedRateAttribute()
    {
        return $this->tax_type === 'percentage'
            ? number_format($this->tax_rate, 2) . '%'
            : number_format($this->tax_rate, 2);
    }

    // Methods
    public function calculateTax($amount, $country = null, $planId = null)
    {
        if (!$this->isApplicable($amount, $country, $planId)) {
            return 0;
        }

        switch ($this->tax_type) {
            case 'percentage':
                $tax = $amount * ($this->tax_rate / 100);
                break;
            case 'fixed':
                $tax = $this->tax_rate;
                break;
            default:
                $tax = 0;
        }

        if ($this->max_amount && $tax > $this->max_amount) {
            $tax = $this->max_amount;
        }

        return $tax;
    }

    public function isApplicable($amount, $country = null, $planId = null)
    {
        if (!$this->is_current) return false;
        if ($amount < $this->min_amount) return false;

        if ($country && $this->applicable_countries && !in_array($country, $this->applicable_countries)) {
            return false;
        }

        if ($planId && $this->applicable_plans && !in_array($planId, $this->applicable_plans)) {
            return false;
        }

        return true;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        return $query->active()
                    ->where('effective_from', '<=', now()->toDateString())
                    ->where(function($q) {
                        $q->whereNull('effective_until')
                          ->orWhere('effective_until', '>=', now()->toDateString());
                    });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
