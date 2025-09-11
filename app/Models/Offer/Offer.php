<?php

namespace App\Models\Offer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Offer extends Model
{
    use HasFactory;

    const ACTIVE = 'active';
    const INACTIVE = 'inactive';

    protected $fillable = [
        'image_path',
        'products_total_price',
        'offer_price',
        'start_date',
        'end_date',
        'status',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'products_offers');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(OfferTranslation::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($offer) {
            $products_total_price = 0;
            foreach ($offer->products as $product) {
                $products_total_price += $product->price;
            }
            $offer->products_total_price = $products_total_price;
        });
    }
}
