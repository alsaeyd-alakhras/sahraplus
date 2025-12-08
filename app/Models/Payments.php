<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payments extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'payment_reference',
        'amount',
        'currency',
        'tax_amount',
        'fee_amount',
        'net_amount',
        'payment_method',
        'gateway',
        'gateway_transaction_id',
        'status',
        'gateway_response',
        'failure_reason',
        'processed_at',
        'failed_at',
        'refunded_at',
        'refunded_amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'gateway_response' => 'array',
        'processed_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            $payment->payment_reference = 'PAY-' . strtoupper(Str::random(12));
        });
    }

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_id');
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => app()->getLocale() === 'ar' ? 'معلق' : 'Pending',
            'processing' => app()->getLocale() === 'ar' ? 'قيد المعالجة' : 'Processing',
            'completed' => app()->getLocale() === 'ar' ? 'مكتمل' : 'Completed',
            'failed' => app()->getLocale() === 'ar' ? 'فاشل' : 'Failed',
            'canceled' => app()->getLocale() === 'ar' ? 'ملغي' : 'Canceled',
            'refunded' => app()->getLocale() === 'ar' ? 'مسترد' : 'Refunded',
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    // Methods
    public function markAsCompleted($gatewayTransactionId = null, $gatewayResponse = null)
    {
        $this->update([
            'status' => 'completed',
            'gateway_transaction_id' => $gatewayTransactionId,
            'gateway_response' => $gatewayResponse,
            'processed_at' => now()
        ]);
    }

    public function markAsFailed($reason = null, $gatewayResponse = null)
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
            'gateway_response' => $gatewayResponse,
            'failed_at' => now()
        ]);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }
}
