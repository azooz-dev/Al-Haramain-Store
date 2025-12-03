<?php

namespace Modules\Catalog\Entities\Category;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Catalog\Database\Factories\Category\CategoryTranslationFactory;

class CategoryTranslation extends Model
{
    use HasFactory;
    
    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return CategoryTranslationFactory::new();
    }
    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'local',
        'name',
        'description',
    ];
}


