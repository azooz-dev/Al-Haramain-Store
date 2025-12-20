<?php

namespace Modules\Order\Entities\Order;

use Modules\User\Entities\User;
use Modules\Coupon\Entities\Coupon\Coupon;
use Modules\Review\Entities\Review\Review;
use Modules\Order\Entities\OrderItem\OrderItem;
use Modules\Order\Enums\OrderStatus;
use Modules\Payment\Entities\Payment\Payment;
use Modules\Payment\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\Address;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Order\Database\Factories\Order\OrderFactory;

class Order extends Model
{
    use HasFactory;

    /**
     * @deprecated Use OrderStatus::PENDING instead
     */
    const PENDING = 'pending';
    /**
     * @deprecated Use OrderStatus::PROCESSING instead
     */
    const PROCESSING = 'processing';
    /**
     * @deprecated Use OrderStatus::SHIPPED instead
     */
    const SHIPPED = 'shipped';
    /**
     * @deprecated Use OrderStatus::DELIVERED instead
     */
    const DELIVERED = 'delivered';
    /**
     * @deprecated Use OrderStatus::CANCELLED instead
     */
    const CANCELLED = 'cancelled';
    /**
     * @deprecated Use OrderStatus::REFUNDED instead
     */
    const REFUNDED = 'refunded';

    /**
     * @deprecated Use PaymentMethod::CASH_ON_DELIVERY instead
     */
    const PAYMENT_METHOD_CASH_ON_DELIVERY = 'cash_on_delivery';
    /**
     * @deprecated Use PaymentMethod::CREDIT_CARD instead
     */
    const PAYMENT_METHOD_CREDIT_CARD = 'credit_card';

    protected $fillable = [
        'user_id',
        'coupon_id',
        'address_id',
        'order_number',
        'total_amount',
        'payment_method',
        'status',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'deleted_at' => 'datetime',
        'status' => OrderStatus::class,
        'payment_method' => PaymentMethod::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get all available statuses
     */
    public static function getStatuses(): array
    {
        return OrderStatus::toArray();
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
     * Get payment status
     */
    public function getPaymentStatusAttribute(): string
    {
        if ($this->payment_method === PaymentMethod::CASH_ON_DELIVERY) {
            return $this->status === OrderStatus::DELIVERED ? 'paid' : 'pending';
        }

        $payment = $this->payments()->latest()->first();
        return $payment?->status ?? 'unknown';
    }

    /**
     * Get payment status color
     */
    public function getPaymentStatusColorAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Get subtotal (before discounts)
     */
    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

    /**
     * Get total discount amount
     */
    public function getTotalDiscountAttribute(): float
    {
        $itemsDiscount = $this->items->sum(function ($item) {
            return $item->discount_price * $item->quantity;
        });

        $couponDiscount = $this->coupon ? $this->coupon->discount_amount : 0;

        return $itemsDiscount + $couponDiscount;
    }

    /**
     * Get total items count
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return $this->status?->canBeCancelled() ?? false;
    }

    /**
     * Check if order can be refunded
     */
    public function canBeRefunded(): bool
    {
        return $this->status?->canBeRefunded() &&
            $this->created_at->diffInDays(now()) <= 30;
    }

    /**
     * Check if order can be edited
     */
    public function canBeEdited(): bool
    {
        return $this->status?->canBeEdited() ?? false;
    }

    /**
     * Get latest payment
     */
    public function getLatestPaymentAttribute(): ?Payment
    {
        return $this->payments()->latest()->first();
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', OrderStatus::PENDING);
    }

    /**
     * Scope for processing orders
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', OrderStatus::PROCESSING);
    }

    /**
     * Scope for shipped orders
     */
    public function scopeShipped($query)
    {
        return $query->where('status', OrderStatus::SHIPPED);
    }

    /**
     * Scope for delivered orders
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', OrderStatus::DELIVERED);
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . date('Y') . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('order_number', $number)->exists());

        return $number;
    }

    /**
     * Boot method to auto-generate order number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
                $order->status = OrderStatus::PENDING;
            }
        });
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return OrderFactory::new();
    }
}
