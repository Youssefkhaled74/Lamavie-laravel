<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Area;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Remove existing rows. Use DELETE (not TRUNCATE) so foreign-key rules
        // such as ON DELETE SET NULL on users.area_id are applied safely.
        DB::table('areas')->delete();

        // Reset auto-increment (optional) so new inserts start from 1.
        // This requires ALTER privilege on some DBs; it's optional and will be
        // skipped silently on failure.
        try {
            DB::statement('ALTER TABLE areas AUTO_INCREMENT = 1');
        } catch (\Throwable $e) {
            // ignore - not critical
        }

        $areas = [
            ['en' => 'Nasr City', 'ar' => 'مدينة نصر', 'percent' => 5.00],
            ['en' => 'Maadi', 'ar' => 'المعادي', 'percent' => 7.00],
            ['en' => 'Heliopolis', 'ar' => 'مصر الجديدة', 'percent' => 6.00],
            ['en' => 'Zamalek', 'ar' => 'الزمالك', 'percent' => 10.00],
            ['en' => 'Downtown', 'ar' => 'وسط البلد', 'percent' => 8.00],
            ['en' => 'Dokki', 'ar' => 'الدقي', 'percent' => 6.00],
            ['en' => 'Mohandessin', 'ar' => 'المهندسين', 'percent' => 6.00],
            ['en' => 'New Cairo', 'ar' => 'القاهرة الجديدة', 'percent' => 12.00],
            ['en' => '6th of October', 'ar' => '6 أكتوبر', 'percent' => 15.00],
            ['en' => 'Shubra', 'ar' => 'شبرا', 'percent' => 4.00],
        ];

        foreach ($areas as $a) {
            Area::create([
                'name' => ['en' => $a['en'], 'ar' => $a['ar']],
                'slug' => Str::slug($a['en']),
                'description' => null,
                'price_increase_percentage' => $a['percent'],
            ]);
        }
    }
}
