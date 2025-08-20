<?php

namespace App\Models\Product;

use App\Models\Category\Category;
use App\Models\Product\ProductImage;
use App\Models\Product\ProductVariant;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'slug',
        'sku',
        'quantity',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }
}
