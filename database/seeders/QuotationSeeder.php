<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Customer;
use App\Models\User;

class QuotationSeeder extends Seeder
{
    public function run(): void
    {
        $storekeeper = User::where('email', 'store@louismarine.qa')->first();
        if (!$storekeeper) return;

        $customer = Customer::firstOrCreate(
            ['email' => 'client@ship.com'],
            ['company_name' => 'Ship Client Ltd'] // Used company_name as per schema
        );

        $quotes = [
           [
               'quotation_number' => 'Q-2025-001',
               'date' => now(),
               'valid_until' => now()->addDays(30),
               'status' => 'draft',
               'subtotal' => 1000,
               'tax' => 0,
               'total' => 1000,
               'reference_number' => 'REF-123',
           ],
           [
               'quotation_number' => 'Q-2025-002',
               'date' => now()->subDays(2),
               'valid_until' => now()->addDays(28),
               'status' => 'sent',
               'subtotal' => 2500,
               'tax' => 0,
               'total' => 2500,
               'reference_number' => 'REF-999',
           ]
        ];

        foreach ($quotes as $qData) {
            $q = Quotation::firstOrCreate(
                ['quotation_number' => $qData['quotation_number']],
                array_merge($qData, [
                    'customer_id' => $customer->id,
                    'created_by' => $storekeeper->id
                ])
            );

            QuotationItem::firstOrCreate(
                ['quotation_id' => $q->id],
                ['description' => 'Marine Spare Part A', 'quantity' => 2, 'unit_price' => 500, 'total' => 1000]
            );
             
            if ($q->total > 1000) {
                 QuotationItem::firstOrCreate(
                    ['quotation_id' => $q->id, 'description' => 'Service Charge'], // unique check needs ID usually
                    ['description' => 'Service Charge', 'quantity' => 1, 'unit_price' => 1500, 'total' => 1500]
                );
            }
        }
        
        $this->command->info('✅ Sample Quotations seeded');
    }
}
