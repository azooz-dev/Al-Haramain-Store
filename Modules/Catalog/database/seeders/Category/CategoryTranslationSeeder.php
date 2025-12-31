<?php

namespace Modules\Catalog\Database\Seeders\Category;

use Modules\Catalog\Entities\Category\CategoryTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $translations = [
            [
                'id' => 1,
                'category_id' => 1,
                'local' => 'en',
                'name' => 'Amber Prayer Beads',
                'description' => 'Explore our exquisite collection of Amber Prayer Beads, crafted from genuine amber. Each piece showcases the unique beauty and warm hues of natural amber, perfect for spiritual reflection or as a distinguished gift.',
            ],
            [
                'id' => 2,
                'category_id' => 1,
                'local' => 'ar',
                'name' => 'سبح الكهرمان',
                'description' => 'استكشف مجموعتنا الفاخرة من سبح الكهرمان المصنوعة من الكهرمان الطبيعي الأصيل. كل قطعة تعكس الجمال الفريد والألوان الدافئة للكهرمان، مثالية للتأمل الروحي أو كهدية مميزة.',
            ],
            [
                'id' => 3,
                'category_id' => 2,
                'local' => 'en',
                'name' => 'Bakelite Prayer Beads',
                'description' => 'Discover our durable and elegant Bakelite Prayer Beads. Known for their smooth finish and rich colors, these prayer beads offer a classic aesthetic and comfortable feel for daily use.',
            ],
            [
                'id' => 4,
                'category_id' => 2,
                'local' => 'ar',
                'name' => 'سبح البكلايت',
                'description' => 'اكتشف سبح البكلايت المتينة والأنيقة لدينا. تتميز بلمسة ناعمة وألوان غنية، وتوفر هذه السبح مظهرًا كلاسيكيًا وشعورًا مريحًا للاستخدام اليومي.',
            ],
            [
                'id' => 5,
                'category_id' => 3,
                'local' => 'en',
                'name' => 'Faturan Prayer Beads',
                'description' => 'Indulge in the timeless charm of Faturan Prayer Beads. Renowned for their unique composition and exquisite patina that develops over time, these beads are a treasure for collectors and enthusiasts.',
            ],
            [
                'id' => 6,
                'category_id' => 3,
                'local' => 'ar',
                'name' => 'سبح الفاتوران',
                'description' => 'استمتع بالجمال الخالد لسبح الفاتوران. تشتهر بتركيبتها الفريدة والعتيقة الرائعة التي تتطور بمرور الوقت، هذه السبح كنز ثمين لهواة الجمع والمتحمسين.',
            ],
            [
                'id' => 7,
                'category_id' => 4,
                'local' => 'en',
                'name' => 'Wooden Prayer Beads',
                'description' => 'Connect with nature through our collection of Wooden Prayer Beads. Crafted from various fine woods, these beads offer a natural, earthy feel and unique grain patterns, perfect for a serene spiritual experience.',
            ],
            [
                'id' => 8,
                'category_id' => 4,
                'local' => 'ar',
                'name' => 'سبح الخشب',
                'description' => 'تواصل مع الطبيعة من خلال مجموعتنا من سبح الخشب. مصنوعة من أجود أنواع الخشب المختلفة، توفر هذه السبح شعورًا طبيعيًا فريدًا ونقوشًا خشبية مميزة، مثالية لتجربة روحية هادئة.',
            ],
            [
                'id' => 9,
                'category_id' => 5,
                'local' => 'en',
                'name' => 'Prayer Rugs',
                'description' => 'Enhance your prayer experience with our beautiful and comfortable Prayer Rugs. Featuring exquisite designs and soft textures, our rugs provide a serene space for your daily prayers.',
            ],
            [
                'id' => 10,
                'category_id' => 5,
                'local' => 'ar',
                'name' => 'سجادات صلاة',
                'description' => 'عزز تجربتك في الصلاة مع سجادات الصلاة الجميلة والمريحة لدينا. تتميز بتصاميم رائعة وملمس ناعم، توفر سجاداتنا مساحة هادئة لصلواتك اليومية.',
            ],
            [
                'id' => 11,
                'category_id' => 6,
                'local' => 'en',
                'name' => 'Personalized Holy Qurans',
                'description' => 'Cherish the Holy Quran with a personal touch. Our collection includes elegantly bound Holy Qurans that can be customized with a name, making them a perfect meaningful gift or a cherished personal item.',
            ],
            [
                'id' => 12,
                'category_id' => 6,
                'local' => 'ar',
                'name' => 'مصاحف مخصصة (أو مصاحف يكتب عليها الاسم)',
                'description' => 'قدِّر المصحف الشريف بلمسة شخصية. تشمل مجموعتنا مصاحف أنيقة يمكن تخصيصها باسم، مما يجعلها هدية ذات معنى أو قطعة شخصية ثمينة.',
            ],
        ];

        foreach ($translations as $translation) {
            CategoryTranslation::updateOrCreate(
                ['id' => $translation['id']],
                $translation
            );
        }
    }
}
