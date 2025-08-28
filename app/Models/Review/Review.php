<?php

namespace App\Models\Review;

use App\Models\User\User;
use App\Models\Order\Order;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;
use App\Models\Review\ReviewTranslation;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    const PENDING = 'pending';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';

    protected $fillable = [
        "user_id",
        "product_id",
        "order_id",
        "rating",
        "comment",
        "status"
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ReviewTranslation::class);
    }

    public function getStatusLabelAttribute()
    {
        return __('app.status.' . $this->status);
    }
}
