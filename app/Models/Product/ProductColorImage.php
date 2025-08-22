<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductColorImage extends Model
{
    protected $fillable = [
        'product_color_id',
        'image_url',
    ];

    public function colors()
    {
        return $this->belongsTo(Product::class);
    }
}
