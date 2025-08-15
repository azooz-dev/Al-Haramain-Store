<?php

namespace Database\Seeders\Review;

use App\Models\Review\ReviewReplay;
use App\Models\Review\Review;
use App\Models\User\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewReplaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReviewReplay::create([
            'review_id' => Review::random()->id,
            'user_id' => User::random()->id,
            'replay' => 'This is a test replay',
        ]);
    }
}
