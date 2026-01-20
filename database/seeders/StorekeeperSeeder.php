<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StorekeeperSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Storekeeper Role
        $storekeeperRole = Role::where('slug', 'storekeeper')->first();
        
        if (!$storekeeperRole) {
            $storekeeperRole = Role::create([
                'name' => 'Store Keeper',
                'slug' => 'storekeeper',
            ]);
        }

        // 2. Create Demo Storekeeper User
        $storekeeper = User::where('email', 'store@louismarine.qa')->first();
        
        if (!$storekeeper) {
            $storekeeper = User::create([
                'name' => 'Store Keeper',
                'email' => 'store@louismarine.qa',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }

        // Assign storekeeper role
        if (!$storekeeper->roles()->where('role_id', $storekeeperRole->id)->exists()) {
            $storekeeper->roles()->attach($storekeeperRole->id);
        }

        // 3. Create Demo Inventory Items
        $items = [
            [
                'item_name' => 'CO₂ Cylinder – 45 Kg',
                'category' => 'CO₂ System',
                'brand' => 'Marine Safety',
                'model' => 'MS-45',
                'unit' => 'Nos',
                'minimum_stock_level' => 5,
                'reorder_quantity' => 10,
                'current_stock' => 12,
                'status' => 'active',
            ],
            [
                'item_name' => 'Fire Extinguisher Valve',
                'category' => 'Fire Extinguisher',
                'brand' => 'SafeGuard',
                'model' => 'SG-V100',
                'unit' => 'Nos',
                'minimum_stock_level' => 10,
                'reorder_quantity' => 20,
                'current_stock' => 8,
                'status' => 'active',
            ],
            [
                'item_name' => 'SCBA Mask',
                'category' => 'SCBA',
                'brand' => 'Drager',
                'model' => 'X-plore 6300',
                'unit' => 'Nos',
                'minimum_stock_level' => 3,
                'reorder_quantity' => 5,
                'current_stock' => 2, // LOW STOCK
                'status' => 'active',
            ],
            [
                'item_name' => 'Hose Pipe – High Pressure',
                'category' => 'Hose & Fittings',
                'brand' => 'FlexFlow',
                'model' => 'HP-50',
                'unit' => 'Mtr',
                'minimum_stock_level' => 20,
                'reorder_quantity' => 50,
                'current_stock' => 25,
                'status' => 'active',
            ],
            [
                'item_name' => 'Pressure Gauge',
                'category' => 'Instruments',
                'brand' => 'Wika',
                'model' => 'PG-100',
                'unit' => 'Nos',
                'minimum_stock_level' => 15,
                'reorder_quantity' => 25,
                'current_stock' => 18,
                'status' => 'active',
            ],
        ];

        foreach ($items as $itemData) {
            // Map string category to ID
            $catName = $itemData['category'];
            unset($itemData['category']); 

            // Find or create category (UUID)
            $category = \App\Models\InventoryCategory::firstOrCreate(
                ['name' => $catName],
                ['slug' => Str::slug($catName)]
            );

            $item = InventoryItem::firstOrCreate(
                ['item_name' => $itemData['item_name']],
                array_merge($itemData, [
                    'created_by' => $storekeeper->id,
                    'inventory_category_id' => $category->id 
                ])
            );

            // Create initial stock movement (opening stock)
            StockMovement::firstOrCreate(
                [
                    'inventory_item_id' => $item->id,
                    'movement_type' => 'inward',
                    'reference_type' => 'Opening Stock'
                ],
                [
                    'quantity' => $item->current_stock,
                    'balance_after' => $item->current_stock,
                    'remarks' => 'Initial stock entry',
                    'created_by' => $storekeeper->id,
                ]
            );
        }

        $this->command->info('✅ Storekeeper role created');
        $this->command->info('✅ Demo storekeeper user created (store@louismarine.qa / password)');
        $this->command->info('✅ 5 demo inventory items created');
        $this->command->info('✅ Stock movements recorded');
    }
}
