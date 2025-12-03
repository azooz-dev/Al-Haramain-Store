<?php

namespace Modules\Catalog\Entities\Category;

use Modules\Catalog\Entities\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Catalog\Database\Factories\Category\CategoryFactory;

class Category extends Model
{
    use HasFactory;
    
    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return CategoryFactory::new();
    }
    protected $fillable = [
        'slug',
        'image',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }
}


