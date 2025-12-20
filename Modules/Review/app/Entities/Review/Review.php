<?php

namespace Modules\Review\Entities\Review;

use Modules\User\Entities\User;
use Modules\Order\Entities\Order\Order;
use Modules\Order\Entities\OrderItem\OrderItem;
use Modules\Review\Enums\ReviewStatus;
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

    /**
     * @deprecated Use ReviewStatus::PENDING instead
     */
    const PENDING = 'pending';
    /**
     * @deprecated Use ReviewStatus::APPROVED instead
     */
    const APPROVED = 'approved';
    /**
     * @deprecated Use ReviewStatus::REJECTED instead
     */
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

    protected $casts = [
        'status' => ReviewStatus::class,
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
        return $this->status?->label() ?? __('app.status.' . $this->attributes['status']);
    }

    /**
     * Get status color for badges
     */
    public function getStatusColorAttribute(): string
    {
        return $this->status?->color() ?? 'gray';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($review) {
            if (empty($review->status)) {
                $review->status = ReviewStatus::PENDING;
            }
        });
    }
}
