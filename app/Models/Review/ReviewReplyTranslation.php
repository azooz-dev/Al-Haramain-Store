<?php

namespace App\Models\Review;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewReplyTranslation extends Model
{
    protected $fillable = [
        'review_reply_id',
        'locale',
        'reply',
    ];

    public function reviewReply(): BelongsTo
    {
        return $this->belongsTo(ReviewReply::class);
    }
}
