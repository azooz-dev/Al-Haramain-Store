<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'image_url',
        'alt_text',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
