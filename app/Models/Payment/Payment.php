<?php

namespace App\Models\Payment;

use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    const PENDING = 'pending';
    const PAID = 'paid';
    const FAILED = 'failed';
    const REFUNDED = 'refunded';

    protected $fillable = [
        'order_id',
        'payment_method',
        'transaction_id',
        'amount',
        'status',
        'paid_at',
    ];


    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
