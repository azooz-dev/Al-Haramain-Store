<?php

namespace Database\Seeders\Review;

use Illuminate\Database\Seeder;
use App\Models\Review\ReviewReply;
use App\Models\Review\ReviewReplyTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ReviewReplyTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReviewReplyTranslation::create([
            'review_reply_id' => ReviewReply::first()->id,
            'locale' => 'en',
            'reply' => 'This is a test reply',
        ]);

        ReviewReplyTranslation::create([
            'review_reply_id' => ReviewReply::find(2)->id,
            'locale' => 'ar',
            'reply' => 'هذا هو تعليق الاختبار',
        ]);
    }
}
