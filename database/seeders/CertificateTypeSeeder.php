<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CertificateType;

class CertificateTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Life Raft Certificate',
                'slug' => 'life-raft',
                'validity_months' => 12,
                'description' => 'For Life Rafts inspection and service.',
            ],
            [
                'name' => 'CO2 Fixed Fire Fighting System',
                'slug' => 'co2-system',
                'validity_months' => 12,
                'description' => 'For CO2 Total Flooding Systems.',
            ],
            [
                'name' => 'Fire Extinguisher Certificate',
                'slug' => 'fire-extinguisher',
                'validity_months' => 12,
                'description' => 'For Portable and Non-Portable Fire Extinguishers.',
            ],
            [
                'name' => 'Breathing Apparatus (SCBA)',
                'slug' => 'scba',
                'validity_months' => 12,
                'description' => 'For Self-Contained Breathing Apparatus inspections.',
            ],
            [
                'name' => 'Lifeboat / Rescue Boat',
                'slug' => 'lifeboat',
                'validity_months' => 12, // Usually 1 year or 5 year
                'description' => 'For Annual or 5-Yearly inspection of Lifeboats/Rescue Boats.',
            ],
            [
                'name' => 'Gas Detection System',
                'slug' => 'gas-detection',
                'validity_months' => 6, // Often 6 months
                'description' => 'For fixed or portable gas detection calibration.',
            ],
        ];

        foreach ($types as $type) {
            CertificateType::updateOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }
    }
}
