<?php

namespace App\Models\Order;

use App\Models\User\User;
use App\Models\Order\OrderItem;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\UserAddresses\Address;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    const PENDING = 'pending';
    const PROCESSING = 'processing';
    const SHIPPED = 'shipped';
    const DELIVERED = 'delivered';
    const CANCELLED = 'cancelled';
    const REFUNDED = 'refunded';

    protected $fillable = [
        'user_id',
        'address_id',
        'order_number',
        'total_amount',
        'payment_method',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
