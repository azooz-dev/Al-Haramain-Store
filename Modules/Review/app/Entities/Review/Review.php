<?php

namespace Modules\Review\Entities\Review;

use Modules\User\Entities\User;
use Modules\Order\Entities\Order\Order;
use Modules\Order\Entities\OrderItem\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Review\Database\Factories\Review\ReviewFactory;

class Review extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return ReviewFactory::new();
    }

    const PENDING = 'pending';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';

    protected $fillable = [
        "user_id",
        "order_id",
        "order_item_id",
        "rating",
        "comment",
        "status",
        "locale",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function getStatusLabelAttribute()
    {
        return __('app.status.' . $this->status);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($review) {
            if (empty($review->status)) {
                $review->status = self::PENDING;
            }
        });
    }
}
