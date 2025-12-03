<?php

namespace Modules\Catalog\Entities\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Catalog\Database\Factories\Product\ProductTranslationFactory;

class ProductTranslation extends Model
{
    use HasFactory;
    
    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return ProductTranslationFactory::new();
    }
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'local',
        'name',
        'description'
    ];
}


