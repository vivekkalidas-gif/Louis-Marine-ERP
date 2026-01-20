<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\WebsiteProduct;
use App\Models\WebsiteCategory;
use Illuminate\Support\Str;

class ImportInventoryToCmsSeeder extends Seeder
{
    public function run()
    {
        $products = Product::where('is_published', true)->with('category')->get();
        $count = 0;

        foreach ($products as $product) {
            // Find or Create corresponding Website Category
            $websiteCategoryId = null;
            // Check for category relationship
            if ($product->category) {
                 $cat = WebsiteCategory::firstOrCreate(
                    ['slug' => \Illuminate\Support\Str::slug($product->category->name)],
                    ['name' => $product->category->name, 'is_active' => true]
                );
                $websiteCategoryId = $cat->id;
            }

            WebsiteProduct::updateOrCreate(
                ['slug' => $product->slug ?? Str::slug($product->name)],
                [
                    'name' => $product->name,
                    'website_category_id' => $websiteCategoryId,
                    'short_description' => $product->short_description ?? Str::limit($product->description, 150),
                    'description' => $product->description,
                    // Handle image_paths (array) to image_path (string)
                    'image_path' => !empty($product->image_paths) ? ($product->image_paths[0] ?? null) : null,
                    'gallery_images' => $product->image_paths ?? [], 
                    // Use existing content if any, otherwise defaults
                    'is_active' => true,
                    'seo_title' => $product->name,
                    'seo_description' => $product->short_description,
                ]
            );
            $count++;
        }

        $this->command->info("Imported {$count} products to Website CMS.");
    }
}
