<?php

namespace Modules\Catalog\Database\Seeders\Category;

use Modules\Catalog\Entities\Category\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'id' => 1,
                'slug' => 'amber-prayer-beads',
                'image' => 'categories/images/01K6K3NCEYEMWSXZXY9W80HRHA.jpg',
                'deleted_at' => null,
                'created_at' => '2025-09-17 15:16:12',
                'updated_at' => '2025-09-17 15:16:12',
            ],
            [
                'id' => 2,
                'slug' => 'bakelite-prayer-beads',
                'image' => 'categories/images/01K6K57M96NPHG919182SV6DG2.jpg',
                'deleted_at' => null,
                'created_at' => '2025-09-17 15:20:58',
                'updated_at' => '2025-09-17 15:20:58',
            ],
            [
                'id' => 3,
                'slug' => 'faturan-prayer-beads',
                'image' => 'categories/images/01K6K53S9W2MRJYJZMB6R10ZJ7.jpg',
                'deleted_at' => null,
                'created_at' => '2025-09-17 15:22:08',
                'updated_at' => '2025-09-17 15:22:08',
            ],
            [
                'id' => 4,
                'slug' => 'wooden-prayer-beads',
                'image' => 'categories/images/01K6K3KBM2TKWYXKD682181E04.jpg',
                'deleted_at' => null,
                'created_at' => '2025-09-17 15:23:24',
                'updated_at' => '2025-09-17 15:23:24',
            ],
            [
                'id' => 5,
                'slug' => 'prayer-rugs',
                'image' => 'categories/images/01K6K5BHH21RVH09SW2NM7HKCF.jpg',
                'deleted_at' => null,
                'created_at' => '2025-09-17 15:24:10',
                'updated_at' => '2025-09-17 15:24:10',
            ],
            [
                'id' => 6,
                'slug' => 'personalized-holy-qurans',
                'image' => 'categories/images/01K6K5A474T67YXSV29NZNC3GR.jpg.jpg',
                'deleted_at' => null,
                'created_at' => '2025-09-17 15:25:07',
                'updated_at' => '2025-09-17 15:25:07',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['id' => $category['id']],
                $category
            );
        }
    }
}
