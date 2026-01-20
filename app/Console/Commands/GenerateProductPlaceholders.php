<?php

namespace App\Console\Commands;

use App\Models\WebsiteProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateProductPlaceholders extends Command
{
    protected $signature = 'products:generate-placeholders';
    protected $description = 'Generate placeholder images for products';

    public function handle()
    {
        $this->info('Generating placeholder images for products...');

        $products = WebsiteProduct::whereNull('image_path')->get();

        if ($products->isEmpty()) {
            $this->info('All products already have images!');
            return 0;
        }

        foreach ($products as $product) {
            // Create a simple SVG placeholder
            $svg = $this->generateSVG($product->name);
            
            // Save to storage
            $filename = 'products/' . $product->slug . '.svg';
            Storage::disk('public')->put($filename, $svg);
            
            // Update product
            $product->update(['image_path' => $filename]);
            
            $this->info("✓ Generated placeholder for: {$product->name}");
        }

        $this->info("\nComplete! Generated " . $products->count() . " placeholder images.");
        return 0;
    }

    private function generateSVG($productName)
    {
        $initials = $this->getInitials($productName);
        $colors = ['#0b1c3c', '#dc2626', '#2563eb', '#059669', '#7c3aed'];
        $color = $colors[array_rand($colors)];

        return <<<SVG
<svg width="400" height="400" xmlns="http://www.w3.org/2000/svg">
    <rect width="400" height="400" fill="#f3f4f6"/>
    <rect x="50" y="50" width="300" height="300" fill="{$color}" rx="10"/>
    <text x="200" y="220" font-family="Arial, sans-serif" font-size="80" font-weight="bold" fill="white" text-anchor="middle">{$initials}</text>
    <text x="200" y="270" font-family="Arial, sans-serif" font-size="16" fill="white" text-anchor="middle" opacity="0.8">LOUIS MARINE</text>
</svg>
SVG;
    }

    private function getInitials($name)
    {
        $words = explode(' ', $name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }
}
