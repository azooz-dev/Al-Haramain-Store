<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductColor extends Model
{
    protected $fillable = [
        'product_id',
        'color_code'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
