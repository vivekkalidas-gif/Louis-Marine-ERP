<?php

namespace Database\Seeders;

use App\Models\WebsiteProduct;
use Illuminate\Database\Seeder;

class ClearProductImagesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Clearing product images...');
        
        WebsiteProduct::query()->update(['image_path' => null]);
        
        $this->command->info('✓ All product images cleared. Placeholders will display.');
        $this->command->info('Total products: ' . WebsiteProduct::count());
    }
}
