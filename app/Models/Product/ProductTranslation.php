<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'local',
        'name',
        'description'
    ];
}
