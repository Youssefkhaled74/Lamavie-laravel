<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarType;
use App\Models\ServiceCategory;

class CarTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the Car Wash service category
        // Adjust the where condition based on your actual service category names
        $carWashCategory = ServiceCategory::whereRaw("JSON_EXTRACT(name, '$.en') = ?", ['Car Wash'])
            ->orWhereRaw("JSON_EXTRACT(name, '$.en') = ?", ['car wash'])
            ->first();

        // If no Car Wash category found, get the first available service category
        if (!$carWashCategory) {
            $carWashCategory = ServiceCategory::first();
        }

        // If still no category, throw an error
        if (!$carWashCategory) {
            $this->command->error('No service categories found. Please seed service categories first.');
            return;
        }

        $carTypes = [
            [
                'name' => [
                    'en' => 'Sedan',
                    'ar' => 'سيدان'
                ],
                'service_category_id' => $carWashCategory->id,
                'price' => 50.00,
            ],
            [
                'name' => [
                    'en' => 'SUV',
                    'ar' => 'دفع رباعي'
                ],
                'service_category_id' => $carWashCategory->id,
                'price' => 70.00,
            ],
            [
                'name' => [
                    'en' => 'Truck',
                    'ar' => 'شاحنة'
                ],
                'service_category_id' => $carWashCategory->id,
                'price' => 80.00,
            ],
        ];

        foreach ($carTypes as $carType) {
            CarType::updateOrCreate(
                [
                    'name->en' => $carType['name']['en'],
                    'service_category_id' => $carType['service_category_id']
                ],
                $carType
            );
        }

        $this->command->info('Car types seeded successfully!');
    }
}
