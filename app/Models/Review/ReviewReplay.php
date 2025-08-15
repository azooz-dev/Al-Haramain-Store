<?php

namespace App\Models\Review;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReviewReplay extends Model
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
}
