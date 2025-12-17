<?php

namespace Modules\Offer\Entities\Offer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Offer\Database\Factories\Offer\OfferTranslationFactory;

class OfferTranslation extends Model
{
    use HasFactory;
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
