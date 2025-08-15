<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'size',
        'color',
        'price',
        'amount_discount_price',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
