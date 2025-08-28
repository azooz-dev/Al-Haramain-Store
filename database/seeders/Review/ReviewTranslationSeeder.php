<?php

namespace Database\Seeders\Review;

use App\Models\Review\Review;
use Illuminate\Database\Seeder;
use App\Models\Review\ReviewTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ReviewTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReviewTranslation::create([
            'review_id' => Review::first()->id,
            'locale' => 'en',
            'comment' => 'This is a test comment',
        ]);

        ReviewTranslation::create([
            'review_id' => Review::find(2)->id,
            'locale' => 'ar',
            'comment' => 'هذا هو تعليق الاختبار',
        ]);
    }
}
