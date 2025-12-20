<?php

namespace Modules\Payment\Entities\Payment;

use Modules\Order\Entities\Order\Order;
use Modules\Payment\Enums\PaymentStatus;
use Modules\Payment\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Payment\Database\Factories\Payment\PaymentFactory;

class Payment extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return PaymentFactory::new();
    }

    /**
     * @deprecated Use PaymentStatus::SUCCESS instead
     */
    const SUCCESS = 'success';
    /**
     * @deprecated Use PaymentStatus::FAILED instead
     */
    const FAILED = 'failed';

    protected $fillable = [
        'order_id',
        'payment_method',
        'transaction_id',
        'amount',
        'status',
        'gateway_response',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'json',
        'paid_at' => 'datetime',
        'status' => PaymentStatus::class,
        'payment_method' => PaymentMethod::class,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get status color for badges
     */
    public function getStatusColorAttribute(): string
    {
        return $this->status?->color() ?? 'gray';
    }

    /**
     * Get status icon
     */
    public function getStatusIconAttribute(): string
    {
        return $this->status?->icon() ?? 'heroicon-o-question-mark-circle';
    }

    /**
     * Check if payment is successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === PaymentStatus::SUCCESS;
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === PaymentStatus::PENDING;
    }
}
