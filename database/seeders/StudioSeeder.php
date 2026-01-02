<?php

namespace Database\Seeders;

use App\Enums\StudioType;
use App\Models\Studio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates exactly 5 studios with specific configurations:
     * - Studio 1 & 2: Regular (10x12, x1.0)
     * - Studio 3 & 4: Premier/Luxury (5x8, x1.5)
     * - Studio 5: 3D (10x12, x1.3)
     */
    public function run(): void
    {
        $studios = [
            [
                'name' => 'Studio 1',
                'type' => StudioType::Regular->value,
                'rows' => 10,
                'cols' => 12,
                'price_multiplier' => 1.00,
            ],
            [
                'name' => 'Studio 2',
                'type' => StudioType::Regular->value,
                'rows' => 10,
                'cols' => 12,
                'price_multiplier' => 1.00,
            ],
            [
                'name' => 'Studio 3',
                'type' => StudioType::Premier->value,
                'rows' => 5,
                'cols' => 8,
                'price_multiplier' => 1.50,
            ],
            [
                'name' => 'Studio 4',
                'type' => StudioType::Premier->value,
                'rows' => 5,
                'cols' => 8,
                'price_multiplier' => 1.50,
            ],
            [
                'name' => 'Studio 5',
                'type' => StudioType::ThreeD->value,
                'rows' => 10,
                'cols' => 12,
                'price_multiplier' => 1.30,
            ],
        ];

        foreach ($studios as $studioData) {
            // Calculate total seats
            $studioData['total_seats'] = $studioData['rows'] * $studioData['cols'];
            
            Studio::updateOrCreate(
                ['name' => $studioData['name']],
                $studioData
            );
        }

        $this->command->info('✅ 5 Studios seeded successfully!');
        $this->command->table(
            ['Name', 'Type', 'Rows', 'Cols', 'Seats', 'Multiplier'],
            collect($studios)->map(fn($s) => [
                $s['name'], 
                $s['type'], 
                $s['rows'], 
                $s['cols'], 
                $s['rows'] * $s['cols'],
                'x' . number_format($s['price_multiplier'], 2)
            ])->toArray()
        );
    }
}
