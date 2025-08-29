<?php

namespace App\Models\Offer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Product\Product;
use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    use HasFactory;

    const FIXED = 'fixed';
    const PERCENTAGE = 'percentage';
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';

    protected $fillable = [
        'name',
        'description',
        'image_path',
        'discount_type',
        'discount_amount',
        'start_date',
        'end_date',
        'status',
        'product_id'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(OfferTranslation::class);
    }
}
