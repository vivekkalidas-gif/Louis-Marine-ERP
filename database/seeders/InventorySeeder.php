<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\InventoryCategory;
use App\Models\Supplier;
use Illuminate\Support\Str;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Categories
        $categories = [
            'Engine Parts',
            'Hull & Deck',
            'Electronics',
            'Safety Equipment',
            'Lubricants & Fluids',
            'Fasteners'
        ];

        foreach ($categories as $cat) {
            InventoryCategory::firstOrCreate(
                ['slug' => Str::slug($cat)],
                ['name' => $cat]
            );
        }

        // Suppliers
        $suppliers = [
            ['name' => 'Marine Supply Co.', 'email' => 'sales@marinesupply.com', 'phone' => '+974 4444 0001'],
            ['name' => 'Global Parts Ltd.', 'email' => 'contact@globalparts.com', 'phone' => '+974 5555 1234'],
            ['name' => 'Doha Hardware', 'email' => 'info@dohahardware.qa', 'phone' => '+974 6666 9876'],
        ];

        foreach ($suppliers as $sup) {
            Supplier::firstOrCreate(
                ['company_name' => $sup['name']],
                ['company_name' => $sup['name'], 'email' => $sup['email'], 'phone' => $sup['phone']]
            );
        }
    }
}
