<?php

namespace Modules\Catalog\Database\Seeders\Product;

use Modules\Catalog\Entities\Product\ProductTranslation;
use Illuminate\Database\Seeder;

class ProductTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $translations = $this->getTranslations();

        foreach ($translations as $translation) {
            ProductTranslation::updateOrCreate(
                ['id' => $translation['id']],
                $translation
            );
        }
    }

    private function getTranslations(): array
    {
        return [
            ['id' => 1, 'product_id' => 1, 'local' => 'en', 'name' => 'Aged Heat-Treated Natural Amber Dhrwi Tesbih - 52g, 45 Beads (13x9mm)', 'description' => '<p>Experience the profound beauty of our <strong>Aged Heat-Treated Natural Amber Dhrwi Tesbih</strong>. This exquisite rosary features 45 meticulously crafted amber beads, each measuring 13x9mm, with a total weight of 52 grams.</p>'],
            ['id' => 2, 'product_id' => 1, 'local' => 'ar', 'name' => 'مسباح كهرمان طبيعي ذروي معتق بالحرارة 52 جرام - 45 خرزة (مقاس 13x9ملم)', 'description' => '<p dir="rtl">استمتع بالجمال العميق لمسباحنا الكهرمان الطبيعي الذروي المعتق بالحرارة. يتميز هذا المسباح الفاخر بـ 45 خرزة كهرمان مصنوعة بدقة.</p>'],
            ['id' => 3, 'product_id' => 2, 'local' => 'en', 'name' => 'Luxury Soft Amber Prayer Beads', 'description' => '<p>Indulge in the refined beauty of our <strong>Luxury Soft Amber Prayer Beads</strong>, specially designed for discerning tastes.</p>'],
            ['id' => 4, 'product_id' => 2, 'local' => 'ar', 'name' => 'سبحة كهرمان نواعم فاخرة', 'description' => '<p dir="rtl">استمتع بالجمال الراقي لسبحة الكهرمان النواعم الفاخرة، المصممة خصيصًا لتناسب الأذواق الرفيعة.</p>'],
            ['id' => 5, 'product_id' => 3, 'local' => 'en', 'name' => 'Luxury Dark Sky Blue Faturan Prayer Beads', 'description' => '<p>Indulge in the unique elegance of our <strong>Luxury Dark Sky Blue Faturan Prayer Beads</strong>.</p>'],
            ['id' => 6, 'product_id' => 3, 'local' => 'ar', 'name' => 'مسباح فاتوران سماوي غامق فاخر', 'description' => '<p dir="rtl">استمتع بالأناقة الفريدة لمسباح الفاتوران السماوي الغامق الفاخر.</p>'],
            ['id' => 7, 'product_id' => 5, 'local' => 'en', 'name' => 'Black Faturan Tasbih Trabzon', 'description' => '<p>An elegant and luxurious tasbih featuring a deep black color.</p>'],
            ['id' => 8, 'product_id' => 5, 'local' => 'ar', 'name' => 'مسباح فاتوران أسود طرابزونة', 'description' => '<p dir="rtl">مسباح فاخر وأنيق يتميز بلونه الأسود الغامق.</p>'],
            ['id' => 9, 'product_id' => 6, 'local' => 'en', 'name' => 'Kuk Wood Prayer Beads 12mm', 'description' => '<p>A rosary made from natural Kuk wood, hand-turned in Egypt.</p>'],
            ['id' => 10, 'product_id' => 6, 'local' => 'ar', 'name' => 'مسباح خشب الكوك 12 ملي', 'description' => '<p dir="rtl">مسبحة مصنوعة من خشب الكوك الطبيعي.</p>'],
            ['id' => 11, 'product_id' => 7, 'local' => 'en', 'name' => 'Ebony Barrel Prayer Beads', 'description' => '<p>A luxurious rosary crafted from natural ebony wood.</p>'],
            ['id' => 12, 'product_id' => 7, 'local' => 'ar', 'name' => 'مسباح خشب الأبنوس البرميلي', 'description' => '<p dir="rtl">مسبحة فاخرة مصنوعة من خشب الأبنوس الطبيعي.</p>'],
            ['id' => 13, 'product_id' => 8, 'local' => 'en', 'name' => 'Dark Maroon Bakelite Prayer Beads', 'description' => '<p>A classic rosary made from genuine Bakelite.</p>'],
            ['id' => 14, 'product_id' => 8, 'local' => 'ar', 'name' => 'مسباح بكلايت عنابي غامق', 'description' => '<p dir="rtl">مسبحة كلاسيكية مصنوعة من مادة البكلايت الأصلي.</p>'],
            ['id' => 15, 'product_id' => 9, 'local' => 'en', 'name' => 'Mint Bakelite Prayer Beads', 'description' => '<p>A unique rosary crafted from genuine Bakelite with mint color.</p>'],
            ['id' => 16, 'product_id' => 9, 'local' => 'ar', 'name' => 'مسباح بكلايت نعناعي', 'description' => '<p dir="rtl">مسبحة فريدة من نوعها مصنوعة من مادة البكلايت الأصلي.</p>'],
            ['id' => 17, 'product_id' => 10, 'local' => 'en', 'name' => 'Chameleon Bakelite Prayer Beads', 'description' => '<p>An exceptional rosary with unique chameleon color-shifting effect.</p>'],
            ['id' => 18, 'product_id' => 10, 'local' => 'ar', 'name' => 'مسباح بكلايت حرباء', 'description' => '<p dir="rtl">مسبحة استثنائية من البكلايت الأصلي بلونها المتغير.</p>'],
            ['id' => 19, 'product_id' => 11, 'local' => 'en', 'name' => 'Velvet Quran with Embroidered Name', 'description' => '<p>An elegant Holy Quran featuring a soft velvet cover with custom embroidery.</p>'],
            ['id' => 20, 'product_id' => 11, 'local' => 'ar', 'name' => 'مصحف بغلاف مخملي مطرز بالاسم', 'description' => '<p dir="rtl">مصحف أنيق ومميز بغلاف مخملي ناعم مطرز.</p>'],
            ['id' => 21, 'product_id' => 12, 'local' => 'en', 'name' => 'Royal Silk Prayer Rug, 3 Million Knots - Futoon - Red', 'description' => '<p>A luxurious prayer rug crafted from high-quality royal silk with 3 million knots.</p>'],
            ['id' => 22, 'product_id' => 12, 'local' => 'ar', 'name' => 'سجادة صلاة الحرير الملكي 3 مليون عُقده - فتون - أحمر', 'description' => '<p dir="rtl">سجادة صلاة فاخرة من الحرير الملكي بكثافة 3 ملايين عقدة.</p>'],
        ];
    }
}
