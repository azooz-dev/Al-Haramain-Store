<?php

namespace Modules\Offer\Database\Seeders\Offer;

use Modules\Offer\Entities\Offer\OfferTranslation;
use Illuminate\Database\Seeder;

class OfferTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $translations = [
            [
                'id' => 1,
                'name' => 'The Devotion Duo',
                'description' => 'Your perfect spiritual companion! This special offer includes a premium prayer beads and a comfortable prayer mat. All you need for moments of peace and devotion.',
                'offer_id' => 1,
                'locale' => 'en',
                'created_at' => '2025-09-17 23:11:15',
                'updated_at' => '2025-09-17 23:11:15',
            ],
            [
                'id' => 2,
                'name' => 'باقة الخشوع',
                'description' => 'استمتع بعرض خاص! احصل على سبحة بكلايت بلون العنابي الفاخر مع سجادة صلاة مريحة. كل ما تحتاجه للحظات من الصفاء والراحة.',
                'offer_id' => 1,
                'locale' => 'ar',
                'created_at' => '2025-09-17 23:11:15',
                'updated_at' => '2025-09-17 23:11:15',
            ],
            [
                'id' => 3,
                'name' => 'An elegant spiritual display',
                'description' => 'Discover spiritual luxury: elegant Mint Bakelite Prayer Beads and a velvet Quran exquisitely embroidered with your name. Your perfect gift or personal spiritual companion.',
                'offer_id' => 3,
                'locale' => 'en',
                'created_at' => '2025-09-18 13:54:38',
                'updated_at' => '2025-09-18 13:54:38',
            ],
            [
                'id' => 4,
                'name' => 'عرض روحانيات أنيقة',
                'description' => 'اكتشف الفخامة الروحانية: مسباح بكلايت نعناعي فاخر ومصحف بغلاف مخملي مطرز باسمك. هديتك المثالية أو رفيقك الروحاني الخاص.',
                'offer_id' => 3,
                'locale' => 'ar',
                'created_at' => '2025-09-18 13:54:38',
                'updated_at' => '2025-09-18 13:54:38',
            ],
            [
                'id' => 5,
                'name' => 'Al-Huda collection offer',
                'description' => 'Red Bakelite prayer beads, personalized velvet Quran, and a luxurious prayer mat. A complete set for worship or a thoughtful gift.',
                'offer_id' => 4,
                'locale' => 'en',
                'created_at' => '2025-09-18 14:46:17',
                'updated_at' => '2025-09-18 14:46:17',
            ],
            [
                'id' => 6,
                'name' => 'عرض مجموعة الهدى',
                'description' => 'مسباح بكلايت أحمر، مصحف مخملي مطرز باسمك، وسجادة صلاة فاخرة. مجموعة متكاملة للعبادة أو كهدية قيمة.',
                'offer_id' => 4,
                'locale' => 'ar',
                'created_at' => '2025-09-18 14:46:17',
                'updated_at' => '2025-09-18 14:46:17',
            ],
        ];

        foreach ($translations as $translation) {
            OfferTranslation::updateOrCreate(
                ['id' => $translation['id']],
                $translation
            );
        }
    }
}
