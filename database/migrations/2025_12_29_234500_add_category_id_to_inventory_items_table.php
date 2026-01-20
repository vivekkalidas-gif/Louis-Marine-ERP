<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add Column (UUID)
        if (!Schema::hasColumn('inventory_items', 'inventory_category_id')) {
            Schema::table('inventory_items', function (Blueprint $table) {
                $table->foreignUuid('inventory_category_id')->nullable()->after('item_name')->constrained('inventory_categories')->nullOnDelete();
            });
        }

        // 2. Migrate Data
        $items = DB::table('inventory_items')->whereNotNull('category')->get();
        foreach ($items as $item) {
            $categoryName = trim($item->category);
            if ($categoryName) {
                $category = DB::table('inventory_categories')->where('name', $categoryName)->first();
                $catId = null;
                
                if (!$category) {
                    $catId = (string) Str::uuid();
                    DB::table('inventory_categories')->insert([
                        'id' => $catId,
                        'name' => $categoryName, 
                        'slug' => Str::slug($categoryName) . '-' . Str::random(4),
                        'created_at' => now(), 
                        'updated_at' => now()
                    ]);
                } else {
                    $catId = $category->id;
                }
                
                // Only update if current value is null (avoid overwriting if re-run) or always update? Always update is safer for data sync.
                DB::table('inventory_items')->where('id', $item->id)->update(['inventory_category_id' => $catId]);
            }
        }

        // 3. Drop Old Column
        if (Schema::hasColumn('inventory_items', 'category')) {
             Schema::table('inventory_items', function (Blueprint $table) {
                // Drop index if exists to avoid errors
                try {
                    $table->dropIndex(['category']);
                } catch (\Exception $e) { /* ignore */ }
                
                $table->dropColumn('category');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->string('category')->nullable();
        });
        
        // Restore data logic is difficult without querying categories, assuming null reversal for now or:
        $items = DB::table('inventory_items')->whereNotNull('inventory_category_id')->get();
        foreach($items as $item) {
             $cat = DB::table('inventory_categories')->find($item->inventory_category_id);
             if($cat) {
                 DB::table('inventory_items')->where('id', $item->id)->update(['category' => $cat->name]);
             }
        }

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropForeign(['inventory_category_id']);
            $table->dropColumn('inventory_category_id');
        });
    }
};
