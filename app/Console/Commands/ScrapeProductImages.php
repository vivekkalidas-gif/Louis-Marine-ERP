<?php

namespace App\Console\Commands;

use App\Models\WebsiteProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ScrapeProductImages extends Command
{
    protected $signature = 'products:scrape-images';
    protected $description = 'Scrape product images from Louis Safety website';

    public function handle()
    {
        $this->info('Scraping product images from louissafety.com...');

        $products = WebsiteProduct::all();

        foreach ($products as $product) {
            $this->info("Processing: {$product->name}");
            
            // Try to find product on Louis Safety website
            $searchUrl = $this->findProductUrl($product->name);
            
            if ($searchUrl) {
                $imageUrl = $this->scrapeProductImage($searchUrl);
                
                if ($imageUrl) {
                    $this->downloadAndSaveImage($product, $imageUrl);
                } else {
                    $this->warn("  ✗ No image found for: {$product->name}");
                }
            } else {
                $this->warn("  ✗ Product page not found: {$product->name}");
            }
        }

        $this->info("\nScraping complete!");
        return 0;
    }

    private function findProductUrl($productName)
    {
        // Map our product names to Louis Safety URLs
        $urlMap = [
            'Adaptor (Italy Type)' => 'https://louissafety.com/product/italy-type-fire-fighting-adaptor/',
            'Fire Fighting Suit' => 'https://louissafety.com/product/fire-fighting-suit/',
            'Breathing Apparatus Set' => 'https://louissafety.com/product/breathing-apparatus-set/',
            'Fire Extinguisher (Dry Powder)' => 'https://louissafety.com/product/dry-powder-stored-pressure-extinguisher/',
            'Lifebuoy with Light' => 'https://louissafety.com/product/bracket-for-lifebuoy-light/',
            'Emergency Escape Breathing Device (EEBD)' => 'https://louissafety.com/product/emergency-escape-breathing-device-eebd/',
            'Fire Hose (Nakajima Type)' => 'https://louissafety.com/product/fire-hose-coupling-nakajima-type/',
            'Immersion Suit' => 'https://louissafety.com/product/immersion-suit/',
            'Life Jacket (SOLAS)' => 'https://louissafety.com/product/life-jacket-solas/',
            'Fire Blanket' => 'https://louissafety.com/product/fire-blanket/',
        ];

        return $urlMap[$productName] ?? null;
    }

    private function scrapeProductImage($url)
    {
        try {
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                return null;
            }

            $html = $response->body();
            
            // Try to find product image in common WordPress/WooCommerce patterns
            if (preg_match('/<img[^>]+class="[^"]*wp-post-image[^"]*"[^>]+src="([^"]+)"/', $html, $matches)) {
                return $matches[1];
            }
            
            if (preg_match('/<img[^>]+src="([^"]+)"[^>]+class="[^"]*wp-post-image[^"]*"/', $html, $matches)) {
                return $matches[1];
            }
            
            // Try Open Graph image
            if (preg_match('/<meta property="og:image" content="([^"]+)"/', $html, $matches)) {
                return $matches[1];
            }

            return null;
        } catch (\Exception $e) {
            $this->error("  Error fetching {$url}: " . $e->getMessage());
            return null;
        }
    }

    private function downloadAndSaveImage($product, $imageUrl)
    {
        try {
            $response = Http::timeout(30)->get($imageUrl);
            
            if (!$response->successful()) {
                $this->warn("  ✗ Failed to download image from: {$imageUrl}");
                return;
            }

            $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = 'products/' . $product->slug . '.' . $extension;
            
            Storage::disk('public')->put($filename, $response->body());
            
            $product->update(['image_path' => $filename]);
            
            $this->info("  ✓ Downloaded and saved image for: {$product->name}");
        } catch (\Exception $e) {
            $this->error("  ✗ Error downloading image: " . $e->getMessage());
        }
    }
}
