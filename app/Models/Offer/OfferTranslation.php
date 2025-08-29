<?php

namespace App\Models\Offer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferTranslation extends Model
{
    protected $fillable = [
        'offer_id',
        'locale',
        'name',
        'description'
    ];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }
}
