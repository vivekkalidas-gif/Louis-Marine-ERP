<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Demo Supplier A',
                'email' => 'supplier.a@example.com',
                'phone' => '1234567890',
                'address' => '123 Supply St, Industrial Area',
                'contact_person' => 'John Doe',
            ],
            [
                'name' => 'Global Parts Ltd',
                'email' => 'sales@globalparts.com',
                'phone' => '0987654321',
                'address' => '456 Warehouse Rd, Logistics City',
                'contact_person' => 'Jane Smith',
            ],
            [
                'name' => 'Tech Solutions Inc',
                'email' => 'contact@techsolutions.com',
                'phone' => '1122334455',
                'address' => '789 Tech Park, Innovation Hub',
                'contact_person' => 'Mike Johnson',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(
                ['email' => $supplier['email']],
                $supplier
            );
        }
    }
}
