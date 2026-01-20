<?php

namespace Database\Seeders;

use App\Models\WebsiteProduct;
use App\Models\WebsiteCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class LouisSafetyProductSeeder extends Seeder
{
    /**
     * Seed products from Louis Safety website
     */
    public function run(): void
    {
        // Sample products from louissafety.com
        $products = [
            [
                'name' => 'Adaptor (Italy Type)',
                'short_description' => 'Italy Type Fire Fighting Adaptor for efficient fire safety connections',
                'description' => 'Italy Type Fire Fighting Adaptors in the UAE: Ensuring Efficient Fire Safety. These adaptors play a crucial role in fire safety systems by ensuring compatibility between different hose coupling types.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Fire Fighting Suit',
                'short_description' => 'Professional fire fighting suit for marine and industrial use',
                'description' => 'High-quality fire fighting suit designed for marine and industrial environments. Provides excellent protection against heat and flames.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Breathing Apparatus Set',
                'short_description' => 'Complete breathing apparatus set for emergency situations',
                'description' => 'Professional breathing apparatus set designed for firefighting and emergency rescue operations. Meets all international safety standards.',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Fire Extinguisher (Dry Powder)',
                'short_description' => 'Dry powder stored pressure fire extinguisher',
                'description' => 'Reliable dry powder fire extinguisher suitable for Class A, B, and C fires. Stored pressure type for immediate use.',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Lifebuoy with Light',
                'short_description' => 'Marine lifebuoy with automatic light',
                'description' => 'SOLAS-approved lifebuoy with automatic self-igniting light for marine safety. Essential lifesaving equipment for vessels.',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Emergency Escape Breathing Device (EEBD)',
                'short_description' => 'Compact emergency escape breathing device',
                'description' => 'Emergency Escape Breathing Device designed for quick escape from hazardous atmospheres. Compact and easy to use.',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Fire Hose (Nakajima Type)',
                'short_description' => 'Nakajima type fire hose for marine applications',
                'description' => 'High-quality fire hose with Nakajima type couplings. Suitable for marine and industrial fire fighting systems.',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Immersion Suit',
                'short_description' => 'Thermal protective immersion suit',
                'description' => 'SOLAS-approved immersion suit providing thermal protection in cold water. Essential safety equipment for marine vessels.',
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'Life Jacket (SOLAS)',
                'short_description' => 'SOLAS-approved life jacket',
                'description' => 'High-visibility SOLAS-approved life jacket with whistle and retro-reflective tape. Suitable for all vessel types.',
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'Fire Blanket',
                'short_description' => 'Fire blanket for emergency fire suppression',
                'description' => 'Fire-resistant blanket for smothering small fires and protecting personnel. Suitable for kitchens and workshops.',
                'is_active' => true,
                'sort_order' => 10,
            ],
        ];

        $this->command->info('Importing products from Louis Safety...');

        foreach ($products as $productData) {
            $productData['slug'] = Str::slug($productData['name']);
            
            // Check if product already exists
            $existing = WebsiteProduct::where('slug', $productData['slug'])->first();
            
            if (!$existing) {
                WebsiteProduct::create($productData);
                $this->command->info("✓ Created: {$productData['name']}");
            } else {
                $this->command->warn("- Skipped (exists): {$productData['name']}");
            }
        }

        $this->command->info("\nImport complete! Total products: " . WebsiteProduct::count());
    }
}
