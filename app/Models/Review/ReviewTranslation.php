<?php

namespace App\Models\Review;

use App\Models\Review\Review;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewTranslation extends Model
{
    protected $fillable = [
        'review_id',
        'locale',
        'comment',
    ];

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }
}
