<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\InventoryCategory;
use App\Models\Product;
use Symfony\Component\DomCrawler\Crawler;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products {--dry-run : Do not save to database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from louismarineqatar.com via WP REST API';

    private $baseUrl = 'https://louismarineqatar.com/wp-json/wp/v2/';
    private $categoryMap = []; // slug => local_id

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting Product Import from {$this->baseUrl}...");

        // 1. Import Categories
        if (!$this->importCategories()) {
            $this->error("Failed to import categories. Aborting.");
            return 1;
        }

        // 2. Import Products
        $this->importProducts();

        $this->info("Import Process Completed!");
        return 0;
    }

    private function importCategories()
    {
        $this->info("Fetching Categories...");
        
        $page = 1;
        $perPage = 100;
        
        do {
            $response = Http::get("{$this->baseUrl}product_cate", [
                'per_page' => $perPage,
                'page' => $page,
            ]);

            if ($response->failed()) {
                $this->error("Failed to fetch categories: " . $response->body());
                return false;
            }

            $categories = $response->json();
            if (empty($categories)) break;

            foreach ($categories as $cat) {
                // Determine Local UUID if exists
                $slug = $cat['slug'];
                $name = $cat['name'];
                
                if ($this->option('dry-run')) {
                    $this->line("[Dry Run] Category: {$name} ({$slug})");
                    $this->categoryMap[$slug] = 'dry-run-id';
                    continue;
                }

                $localCat = InventoryCategory::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => html_entity_decode($name),
                        'description' => $cat['description'] ?? null,
                    ]
                );
                
                $this->categoryMap[$slug] = $localCat->id;
                $this->info("Processed Category: {$name}");
            }

            $page++;
        } while (count($categories) === $perPage);

        return true;
    }

    private function importProducts()
    {
        $this->info("Fetching Products...");

        $page = 1;
        $perPage = 10; // Smaller batch for products to handle images
        
        do {
            $this->info("Fetching Page {$page}...");
            $response = Http::get("{$this->baseUrl}product", [
                'per_page' => $perPage,
                'page' => $page,
                '_embed' => true, // Include media
            ]);

            if ($response->failed()) {
                // If 400 bad request (often means page out of bounds), we are done.
                if ($response->status() === 400) {
                    break;
                }
                $this->error("Failed to fetch products page {$page}: " . $response->body());
                break;
            }

            $products = $response->json();
            if (empty($products)) break;

            foreach ($products as $wpProduct) {
                $this->processProduct($wpProduct);
            }

            $page++;
        } while (count($products) > 0);
    }

    private function processProduct($data)
    {
        try {
            $title = html_entity_decode($data['title']['rendered'] ?? $data['title'] ?? 'Untitled');
            $slug = $data['slug'];
            $content = $data['content']['rendered'] ?? $data['excerpt']['rendered'] ?? '';
            
            $this->info("Processing: {$title}");

            // 1. Extract Specifications (Naive HTML parsing)
            $specifications = $this->parseSpecifications($content);

            // 2. Handle Images
            $imagePaths = [];
            $featuredMedia = $data['_embedded']['wp:featuredmedia'][0] ?? null;
            
            if ($featuredMedia && isset($featuredMedia['source_url'])) {
                $imageUrl = $featuredMedia['source_url'];
                
                if ($this->option('dry-run')) {
                     $this->line("[Dry Run] Would download image: {$imageUrl}");
                     $imagePaths = ["products/{$slug}/main.jpg"];
                } else {
                    $localPath = $this->downloadImage($imageUrl, $slug); 
                     if ($localPath) {
                        $imagePaths = [$localPath];
                    }
                }
            }
        
            // 3. Map Category
            $localCatId = null;
            $embeddedTerms = $data['_embedded']['wp:term'] ?? [];
            foreach ($embeddedTerms as $taxonomy) {
                foreach ($taxonomy as $term) {
                    if ($term['taxonomy'] === 'product_cate') {
                        $catSlug = $term['slug'];
                        if (isset($this->categoryMap[$catSlug])) {
                            $localCatId = $this->categoryMap[$catSlug];
                            break 2; 
                        }
                    }
                }
            }

            if ($this->option('dry-run')) {
                $this->line("[Dry Run] Product: {$title} (Cat ID: {$localCatId})");
                return;
            }
            
            Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $title,
                    'short_description' => Str::limit(strip_tags($data['excerpt']['rendered'] ?? $content), 150),
                    'description' => $content,
                    'specifications' => $specifications,
                    'image_paths' => $imagePaths,
                    'inventory_category_id' => $localCatId,
                    'is_published' => true,
                    'sku' => strtoupper(Str::slug($title)),
                ]
            );

        } catch (\Exception $e) {
            $this->error("Error processing product ID " . ($data['id'] ?? 'unknown') . ": " . $e->getMessage());
        }
    }

    private function parseSpecifications($html)
    {
        $specs = [];
        if (empty($html)) return $specs;

        try {
            $crawler = new Crawler($html);
            // Look for tables
            $crawler->filter('table tr')->each(function (Crawler $node) use (&$specs) {
                $tds = $node->filter('td');
                if ($tds->count() >= 2) {
                    $key = trim($tds->eq(0)->text());
                    $val = trim($tds->eq(1)->text());
                    if ($key && $val) {
                        $specs[$key] = $val;
                    }
                }
            });
        } catch (\Exception $e) {
            // content might not be valid HTML or other issue
        }
        return $specs;
    }

    private function downloadImage($url, $slug)
    {
        try {
            $contents = Http::get($url)->body();
            $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = "products/{$slug}/main.{$ext}"; // clean structure
            
            Storage::disk('public')->put($filename, $contents);
            return $filename;
        } catch (\Exception $e) {
            $this->error("Failed to download image for {$slug}: " . $e->getMessage());
            return null;
        }
    }
}
