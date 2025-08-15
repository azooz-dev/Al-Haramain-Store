<?php

namespace App\Models\Category;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'image',
        'description',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
