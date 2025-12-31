<?php

namespace Modules\Catalog\Database\Seeders\Product;

use Modules\Catalog\Entities\Product\ProductColorImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductColorImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productColorImages = [
            ['id' => 1, 'product_color_id' => 1, 'image_url' => 'products/images/01K6JZYQR8BPDE3AR3JYE70AS3.jpg', 'alt_text' => null, 'created_at' => '2025-09-17 15:34:56', 'updated_at' => '2025-09-17 15:34:56'],
            ['id' => 2, 'product_color_id' => 1, 'image_url' => 'products/images/01K6JZYQRH5NW2QMWH9YYHDH7Y.jpg', 'alt_text' => null, 'created_at' => '2025-09-17 15:34:56', 'updated_at' => '2025-09-17 15:34:56'],
            ['id' => 3, 'product_color_id' => 1, 'image_url' => 'products/images/01K5CFAMNB1G6BRA2QZ9AHR19A.jpg', 'alt_text' => null, 'created_at' => '2025-09-17 16:31:50', 'updated_at' => '2025-09-17 16:31:50'],
            ['id' => 4, 'product_color_id' => 1, 'image_url' => 'products/images/01K5CFAMNEES9BAZKRH6R2M85P.jpg', 'alt_text' => null, 'created_at' => '2025-09-17 16:31:50', 'updated_at' => '2025-09-17 16:31:50'],
            ['id' => 5, 'product_color_id' => 2, 'image_url' => 'products/images/01K6JXG3QX888HDF9ZA6R88004.webp', 'alt_text' => null, 'created_at' => '2025-09-17 16:41:49', 'updated_at' => '2025-09-17 16:41:49'],
            ['id' => 6, 'product_color_id' => 2, 'image_url' => 'products/images/01K6JXG3RQCK74HABN7CJT7BJN.webp', 'alt_text' => null, 'created_at' => '2025-09-17 16:41:49', 'updated_at' => '2025-09-17 16:41:49'],
            ['id' => 7, 'product_color_id' => 2, 'image_url' => 'products/images/01K5CJJTC9KYVC4160AMZTFN48.jpg', 'alt_text' => null, 'created_at' => '2025-09-17 16:41:49', 'updated_at' => '2025-09-17 16:41:49'],
            ['id' => 8, 'product_color_id' => 2, 'image_url' => 'products/images/01K6JXG3R9YB0SHH92HCTTSJSG.jpg', 'alt_text' => null, 'created_at' => '2025-09-17 16:41:49', 'updated_at' => '2025-09-17 16:41:49'],
            ['id' => 9, 'product_color_id' => 2, 'image_url' => 'products/images/01K6JXG3SJ9EYFVS3PH71WN4CB.webp', 'alt_text' => null, 'created_at' => '2025-09-17 16:41:49', 'updated_at' => '2025-09-17 16:41:49'],
            ['id' => 10, 'product_color_id' => 2, 'image_url' => 'products/images/01K6JXG3S8PRFAJSW0QZ45XG80.webp', 'alt_text' => null, 'created_at' => '2025-09-17 16:41:49', 'updated_at' => '2025-09-17 16:41:49'],
            ['id' => 11, 'product_color_id' => 3, 'image_url' => 'products/images/01K6K231XK4PEDG22WMZDE7YV1.webp', 'alt_text' => null, 'created_at' => '2025-09-17 17:07:33', 'updated_at' => '2025-09-17 17:07:33'],
            ['id' => 12, 'product_color_id' => 3, 'image_url' => 'products/images/01K6K231XW1KBF063364E5H1HR.webp', 'alt_text' => null, 'created_at' => '2025-09-17 17:07:33', 'updated_at' => '2025-09-17 17:07:33'],
            ['id' => 13, 'product_color_id' => 3, 'image_url' => 'products/images/01K6K231Y6SMW9XCMNJ5FY80NC.jpg', 'alt_text' => null, 'created_at' => '2025-09-17 17:07:33', 'updated_at' => '2025-09-17 17:07:33'],
            ['id' => 14, 'product_color_id' => 3, 'image_url' => 'products/images/01K6K231YGZVZZQ445Q0JX11MP.jpg', 'alt_text' => null, 'created_at' => '2025-09-17 17:07:33', 'updated_at' => '2025-09-17 17:07:33'],
            ['id' => 15, 'product_color_id' => 5, 'image_url' => 'products/images/01K6K25SA60WNYT7AN0TFTXTK8.webp', 'alt_text' => null, 'created_at' => '2025-09-17 17:17:17', 'updated_at' => '2025-09-17 17:17:17'],
            ['id' => 16, 'product_color_id' => 5, 'image_url' => 'products/images/01K6K25SAFQE07DS1BBSESBWQW.webp', 'alt_text' => null, 'created_at' => '2025-09-17 17:17:17', 'updated_at' => '2025-09-17 17:17:17'],
            ['id' => 17, 'product_color_id' => 6, 'image_url' => 'products/images/01K6K28SB01FS2TRPFSXKVMHCJ.webp', 'alt_text' => null, 'created_at' => '2025-09-17 17:43:12', 'updated_at' => '2025-09-17 17:43:12'],
            ['id' => 18, 'product_color_id' => 6, 'image_url' => 'products/images/01K6K28SBAB4QJT66V5BMHDTR1.webp', 'alt_text' => null, 'created_at' => '2025-09-17 17:43:12', 'updated_at' => '2025-09-17 17:43:12'],
            ['id' => 19, 'product_color_id' => 7, 'image_url' => 'products/images/01K6K2E6QG20SFQ7A8P297J07B.webp', 'alt_text' => null, 'created_at' => '2025-09-17 17:47:41', 'updated_at' => '2025-09-17 17:47:41'],
            ['id' => 20, 'product_color_id' => 7, 'image_url' => 'products/images/01K6K2E6QQT5JFXFK6B1JAAZDW.webp', 'alt_text' => null, 'created_at' => '2025-09-17 17:47:41', 'updated_at' => '2025-09-17 17:47:41'],
            ['id' => 21, 'product_color_id' => 8, 'image_url' => 'products/images/01K6K2G9XTK5PX4BCEYXB9SG0E.webp', 'alt_text' => null, 'created_at' => '2025-09-17 17:51:37', 'updated_at' => '2025-09-17 17:51:37'],
            ['id' => 22, 'product_color_id' => 8, 'image_url' => 'products/images/01K6K2G9Y3VHJ67JM2593TJ7AR.webp', 'alt_text' => null, 'created_at' => '2025-09-17 17:51:37', 'updated_at' => '2025-09-17 17:51:37'],
            ['id' => 23, 'product_color_id' => 9, 'image_url' => 'products/images/01K6K2JE3Z5NDD5BQ7NAHW4465.webp', 'alt_text' => null, 'created_at' => '2025-09-17 17:56:11', 'updated_at' => '2025-09-17 17:56:11'],
            ['id' => 24, 'product_color_id' => 9, 'image_url' => 'products/images/01K6K2JE47ZXDHYJRSDSBW099H.webp', 'alt_text' => null, 'created_at' => '2025-09-17 17:56:11', 'updated_at' => '2025-09-17 17:56:11'],
            ['id' => 25, 'product_color_id' => 10, 'image_url' => 'products/images/01K6K2P18JV6CXNKD0RC8XRWYV.webp', 'alt_text' => null, 'created_at' => '2025-09-17 17:59:56', 'updated_at' => '2025-09-17 17:59:56'],
            ['id' => 26, 'product_color_id' => 10, 'image_url' => 'products/images/01K6K2P18TYJDXNPP87K502E5G.webp', 'alt_text' => null, 'created_at' => '2025-09-17 17:59:56', 'updated_at' => '2025-09-17 17:59:56'],
            ['id' => 27, 'product_color_id' => 10, 'image_url' => 'products/images/01K6K2P188569P0PVFD38SVSZA.webp', 'alt_text' => null, 'created_at' => '2025-09-17 17:59:56', 'updated_at' => '2025-09-17 17:59:56'],
            ['id' => 28, 'product_color_id' => 11, 'image_url' => 'products/images/01K6K2QKZ74F57BWPNY15G25F7.webp', 'alt_text' => null, 'created_at' => '2025-09-17 18:33:15', 'updated_at' => '2025-09-17 18:33:15'],
            ['id' => 29, 'product_color_id' => 12, 'image_url' => 'products/images/01K6K2T8SCD2SYJVJN9W2RCV7A.jpg', 'alt_text' => null, 'created_at' => '2025-09-17 18:40:43', 'updated_at' => '2025-09-17 18:40:43'],
            ['id' => 30, 'product_color_id' => 12, 'image_url' => 'products/images/01K6K2T8SMK2JKWG46VX4GNQ6Q.jpg', 'alt_text' => null, 'created_at' => '2025-09-17 18:40:43', 'updated_at' => '2025-09-17 18:40:43'],
            ['id' => 31, 'product_color_id' => 12, 'image_url' => 'products/images/01K6K2T8STPMX1KGSHGHG7D0H4.jpg', 'alt_text' => null, 'created_at' => '2025-09-17 18:40:43', 'updated_at' => '2025-09-17 18:40:43'],
        ];

        foreach ($productColorImages as $image) {
            ProductColorImage::updateOrCreate(
                ['id' => $image['id']],
                $image
            );
        }
    }
}
