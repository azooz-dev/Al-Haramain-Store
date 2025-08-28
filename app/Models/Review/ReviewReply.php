<?php

namespace App\Models\Review;

use App\Models\User\User;
use App\Models\Review\Review;
use Illuminate\Database\Eloquent\Model;
use App\Models\Review\ReviewReplyTranslation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReviewReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'user_id',
        'replay',
    ];

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ReviewReplyTranslation::class);
    }
}
