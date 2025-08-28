<?php

namespace Database\Seeders\Review;

use App\Models\Review\ReviewReply;
use App\Models\Review\Review;
use App\Models\User\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewReplySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReviewReply::create([
            'review_id' => Review::first()->id,
            'user_id' => User::first()->id,
        ]);

        ReviewReply::create([
            'review_id' => Review::find(2)->id,
            'user_id' => User::first()->id,
        ]);
    }
}
