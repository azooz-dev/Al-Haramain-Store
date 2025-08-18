<?php

namespace App\Models\Category;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryTranslation extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'local',
        'description',
    ];

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }
}
