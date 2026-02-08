<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use App\Models\YourItems;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class YourItemsPricingSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'قميص كتان', 'item_en' => 'LINEN Shirt', 'washing_price' => 55, 'ironing_price' => 33],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'قميص', 'item_en' => 'Shirt', 'washing_price' => 50, 'ironing_price' => 30],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'تي شيرت', 'item_en' => 'T-Shirt', 'washing_price' => 55, 'ironing_price' => 33],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'بلوزة', 'item_en' => 'Blouse', 'washing_price' => 60, 'ironing_price' => 36],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'هودي', 'item_en' => 'Hoodie', 'washing_price' => 100, 'ironing_price' => 60],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'سويت شيرت', 'item_en' => 'Sweatshirt', 'washing_price' => 100, 'ironing_price' => 60],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'بنطلون بدلة', 'item_en' => 'Suit Pants', 'washing_price' => 120, 'ironing_price' => 72],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'بدلة جلد', 'item_en' => 'Leather Suit', 'washing_price' => 150, 'ironing_price' => 90],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'بنطلون', 'item_en' => 'Pants', 'washing_price' => 55, 'ironing_price' => 33],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'شورت', 'item_en' => 'Shorts', 'washing_price' => 40, 'ironing_price' => 24],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'جيبة جلد', 'item_en' => 'Leather Skirt', 'washing_price' => 150, 'ironing_price' => 90],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'جيبة', 'item_en' => 'Skirt', 'washing_price' => 80, 'ironing_price' => 48],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'بدلة', 'item_en' => 'Suit', 'washing_price' => 280, 'ironing_price' => 168],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'جاكيت بدلة', 'item_en' => 'Suit Jacket', 'washing_price' => 160, 'ironing_price' => 96],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'فيست', 'item_en' => 'Vest', 'washing_price' => 80, 'ironing_price' => 48],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'فستان', 'item_en' => 'Dress', 'washing_price' => 180, 'ironing_price' => 108],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'فستان كاجوال', 'item_en' => 'Casual Dress', 'washing_price' => 150, 'ironing_price' => 90],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'فستان سهرة', 'item_en' => 'Dress Soiree', 'washing_price' => 250, 'ironing_price' => 150],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'جمبسوت', 'item_en' => 'Jumpsuit', 'washing_price' => 120, 'ironing_price' => 72],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'جاكيت صوف', 'item_en' => 'Wool Jacket', 'washing_price' => 200, 'ironing_price' => 120],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'جاكيت جلد طبيعي', 'item_en' => 'Natural Leather Jacket', 'washing_price' => 700, 'ironing_price' => 420],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'جاكيت بومبر', 'item_en' => 'Bomber Jacket', 'washing_price' => 350, 'ironing_price' => 210],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'جاكيت عادي', 'item_en' => 'Normal Jacket', 'washing_price' => 160, 'ironing_price' => 96],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'عباية عادية', 'item_en' => 'Abaya Normal', 'washing_price' => 150, 'ironing_price' => 90],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'عباية صوف', 'item_en' => 'Abaya WOOL', 'washing_price' => 180, 'ironing_price' => 108],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'معطف صوف', 'item_en' => 'COAT WOOL', 'washing_price' => 260, 'ironing_price' => 156],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'معطف', 'item_en' => 'Coat', 'washing_price' => 180, 'ironing_price' => 108],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'جاكيت فرو', 'item_en' => 'Fur Jacket', 'washing_price' => 350, 'ironing_price' => 210],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'بلوفر', 'item_en' => 'Sweater', 'washing_price' => 160, 'ironing_price' => 96],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'تونيك', 'item_en' => 'Tonic', 'washing_price' => 65, 'ironing_price' => 39],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'كاب', 'item_en' => 'Cape', 'washing_price' => 50, 'ironing_price' => 30],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'زي جراحة', 'item_en' => 'Surgery Suit', 'washing_price' => 90, 'ironing_price' => 54],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'فستان زفاف', 'item_en' => 'Wedding Dress', 'washing_price' => 800, 'ironing_price' => 480],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'جاكيت جلد', 'item_en' => 'Leather Jacket', 'washing_price' => 350, 'ironing_price' => 210],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'جاكيت شمواه', 'item_en' => 'SHAMWAH Jacket', 'washing_price' => 350, 'ironing_price' => 210],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'تريكو خفيف', 'item_en' => 'Light Knitwear', 'washing_price' => 120, 'ironing_price' => 72],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'تريكو ثقيل', 'item_en' => 'Heavy Knitwear', 'washing_price' => 180, 'ironing_price' => 108],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'بلوفر', 'item_en' => 'Pullover', 'washing_price' => 100, 'ironing_price' => 60],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'كارديجان طويل', 'item_en' => 'Long Cardigan', 'washing_price' => 250, 'ironing_price' => 150],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'كارديجان', 'item_en' => 'Cardigan', 'washing_price' => 200, 'ironing_price' => 120],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'ربطة عنق', 'item_en' => 'Tie', 'washing_price' => 25, 'ironing_price' => 15],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'شال', 'item_en' => 'Scarf', 'washing_price' => 30, 'ironing_price' => 18],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'باشمينا', 'item_en' => 'Pashmina', 'washing_price' => 100, 'ironing_price' => 60],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'شال صوف', 'item_en' => 'Shawl', 'washing_price' => 80, 'ironing_price' => 48],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'بيجاما', 'item_en' => 'Pijama', 'washing_price' => 120, 'ironing_price' => 72],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'جلابية', 'item_en' => 'Galabiya', 'washing_price' => 100, 'ironing_price' => 60],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'بدلة رياضية', 'item_en' => 'Training Suit', 'washing_price' => 150, 'ironing_price' => 90],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'شرابات', 'item_en' => 'Socks', 'washing_price' => 15, 'ironing_price' => 9],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'ملابس داخلية رجالي', 'item_en' => 'Under Wear Men', 'washing_price' => 15, 'ironing_price' => 9],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'ملابس داخلية حريمي', 'item_en' => 'Under Wear Women', 'washing_price' => 15, 'ironing_price' => 9],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'بدلة غطس', 'item_en' => 'Diving Suit', 'washing_price' => 150, 'ironing_price' => 90],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'ملابس سباحة', 'item_en' => 'Swim Wear', 'washing_price' => 50, 'ironing_price' => 30],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'حزام', 'item_en' => 'Belt', 'washing_price' => 25, 'ironing_price' => 0],
            ['category_ar' => 'ملابس وأقمشة', 'category_en' => 'Clothing & Textiles', 'item_ar' => 'جوانتي', 'item_en' => 'Gloves', 'washing_price' => 15, 'ironing_price' => 9],
            ['category_ar' => 'شنط وأحذية', 'category_en' => 'Bags & Shoes', 'item_ar' => 'شنطة جلد', 'item_en' => 'Leather Bag', 'washing_price' => 150, 'ironing_price' => 90],
            ['category_ar' => 'شنط وأحذية', 'category_en' => 'Bags & Shoes', 'item_ar' => 'شنطة رياضية', 'item_en' => 'Sport Bag', 'washing_price' => 150, 'ironing_price' => 90],
            ['category_ar' => 'شنط وأحذية', 'category_en' => 'Bags & Shoes', 'item_ar' => 'شنطة مدرسة', 'item_en' => 'School Bag', 'washing_price' => 100, 'ironing_price' => 60],
            ['category_ar' => 'شنط وأحذية', 'category_en' => 'Bags & Shoes', 'item_ar' => 'شنطة حريمي', 'item_en' => 'Lady Bag', 'washing_price' => 100, 'ironing_price' => 60],
            ['category_ar' => 'شنط وأحذية', 'category_en' => 'Bags & Shoes', 'item_ar' => 'شنطة سفر', 'item_en' => 'Luggage Bag', 'washing_price' => 200, 'ironing_price' => 120],
            ['category_ar' => 'شنط وأحذية', 'category_en' => 'Bags & Shoes', 'item_ar' => 'مقلمة', 'item_en' => 'Pencil Case', 'washing_price' => 20, 'ironing_price' => 12],
            ['category_ar' => 'سجاد', 'category_en' => 'Carpets', 'item_ar' => 'سجاد يدوي وأنتيك', 'item_en' => 'Hand Made & Antique Carpet', 'washing_price' => 60, 'ironing_price' => 36],
            ['category_ar' => 'سجاد', 'category_en' => 'Carpets', 'item_ar' => 'سجاد شنواه', 'item_en' => 'Hand Made – Shnwah', 'washing_price' => 55, 'ironing_price' => 33],
            ['category_ar' => 'سجاد', 'category_en' => 'Carpets', 'item_ar' => 'سجاد كاشان', 'item_en' => 'Hand Made – Kashan', 'washing_price' => 55, 'ironing_price' => 33],
            ['category_ar' => 'سجاد', 'category_en' => 'Carpets', 'item_ar' => 'سجاد أصفهان', 'item_en' => 'Hand Made – Asfahan', 'washing_price' => 55, 'ironing_price' => 33],
            ['category_ar' => 'سجاد', 'category_en' => 'Carpets', 'item_ar' => 'سجاد صوف وحرير', 'item_en' => 'Hand Made – Wool & Silk', 'washing_price' => 55, 'ironing_price' => 33],
            ['category_ar' => 'سجاد', 'category_en' => 'Carpets', 'item_ar' => 'سجاد كيليم', 'item_en' => 'Kilim – Silk – Shag – Hand', 'washing_price' => 45, 'ironing_price' => 27],
            ['category_ar' => 'ستائر', 'category_en' => 'Curtains', 'item_ar' => 'ستارة ثقيلة', 'item_en' => 'Curtain HEAVY', 'washing_price' => 45, 'ironing_price' => 27],
            ['category_ar' => 'ستائر', 'category_en' => 'Curtains', 'item_ar' => 'ستارة خفيفة', 'item_en' => 'Curtain LIGHT', 'washing_price' => 25, 'ironing_price' => 15],
        ];

        $normalize = function (?string $value): string {
            if ($value === null) {
                return '';
            }
            $value = trim($value);
            $value = preg_replace('/\s+/', ' ', $value ?? '');
            $value = str_replace('&', 'and', $value ?? '');
            if (function_exists('mb_strtolower')) {
                return mb_strtolower($value ?? '');
            }
            return strtolower($value ?? '');
        };

        $categories = ServiceCategory::all();
        $categoryIndex = $categories->map(function ($category) use ($normalize) {
            $name = is_array($category->name) ? $category->name : [];
            $en = $name['en'] ?? '';
            $ar = $name['ar'] ?? '';
            return [
                'id' => $category->id,
                'en' => $normalize($en),
                'ar' => $normalize($ar),
                'en_raw' => $en,
                'ar_raw' => $ar,
            ];
        });

        $findCategoryId = function (string $categoryEn, string $categoryAr) use ($categoryIndex, $normalize): ?int {
            $en = $normalize($categoryEn);
            $ar = $normalize($categoryAr);

            foreach ($categoryIndex as $category) {
                if ($en !== '' && $category['en'] !== '' && $category['en'] === $en) {
                    return $category['id'];
                }
            }

            foreach ($categoryIndex as $category) {
                if ($ar !== '' && $category['ar'] !== '' && $category['ar'] === $ar) {
                    return $category['id'];
                }
            }

            foreach ($categoryIndex as $category) {
                if ($en !== '' && $category['en'] !== '' && (str_contains($category['en'], $en) || str_contains($en, $category['en']))) {
                    return $category['id'];
                }
            }

            foreach ($categoryIndex as $category) {
                if ($ar !== '' && $category['ar'] !== '' && (str_contains($category['ar'], $ar) || str_contains($ar, $category['ar']))) {
                    return $category['id'];
                }
            }

            return null;
        };

        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }
        YourItems::truncate();
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($items as $index => $row) {
            $categoryAr = trim((string) ($row['category_ar'] ?? ''));
            $categoryEn = trim((string) ($row['category_en'] ?? ''));
            $itemAr = trim((string) ($row['item_ar'] ?? ''));
            $itemEn = trim((string) ($row['item_en'] ?? ''));
            $washingPrice = $row['washing_price'] ?? null;
            $ironingPrice = $row['ironing_price'] ?? null;

            if ($itemAr === '' && $itemEn === '') {
                $skipped++;
                continue;
            }

            $categoryId = $findCategoryId($categoryEn, $categoryAr);
            if (! $categoryId) {
                $rowNumber = $index + 2;
                $this->command?->warn("Category not found for row {$rowNumber}: {$categoryEn} / {$categoryAr}");
                $skipped++;
                continue;
            }

            $data = [
                'name' => [
                    'ar' => $itemAr !== '' ? $itemAr : $itemEn,
                    'en' => $itemEn !== '' ? $itemEn : $itemAr,
                ],
                'service_category_id' => $categoryId,
                'washing_price' => $washingPrice !== null && $washingPrice !== '' ? (float) $washingPrice : null,
                'ironing_price' => $ironingPrice !== null && $ironingPrice !== '' ? (float) $ironingPrice : null,
                'price' => $washingPrice !== null && $washingPrice !== '' ? (float) $washingPrice : null,
            ];

            $existing = YourItems::where('service_category_id', $categoryId)
                ->where(function ($query) use ($itemEn, $itemAr) {
                    if ($itemEn !== '') {
                        $query->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) = ?", [$itemEn]);
                    }
                    if ($itemAr !== '') {
                        $query->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) = ?", [$itemAr]);
                    }
                })
                ->first();

            if ($existing) {
                $existing->update($data);
                $updated++;
            } else {
                YourItems::create($data);
                $created++;
            }
        }

        $this->command?->info("Your items seeded. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}");
    }
}
