<?php

namespace Modules\Offer\Entities\Offer;

use Modules\Catalog\Entities\Product\Product;
use Modules\Catalog\Entities\Product\ProductColor;
use Modules\Catalog\Entities\Product\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferProduct extends Model
{
  protected $table = 'products_offers';

  protected $fillable = [
    'offer_id',
    'product_id',
    'product_variant_id',
    'product_color_id',
    'variant_price',
    'quantity',
  ];

  protected $casts = [
    'offer_id' => 'integer',
    'product_id' => 'integer',
    'product_variant_id' => 'integer',
    'product_color_id' => 'integer',
    'variant_price' => 'decimal:2',
    'quantity' => 'integer',
  ];

  public function offer(): BelongsTo
  {
    return $this->belongsTo(Offer::class);
  }

  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class);
  }

  public function productVariant(): BelongsTo
  {
    return $this->belongsTo(ProductVariant::class);
  }

  public function productColor(): BelongsTo
  {
    return $this->belongsTo(ProductColor::class);
  }

  /**
   * Get the total price for this offer product
   */
  public function getTotalPriceAttribute(): float
  {
    return $this->variant_price * $this->quantity;
  }
}
