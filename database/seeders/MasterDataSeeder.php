<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Vessel;
use App\Models\CertificateTemplate;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Customers & Vessels
        $customers = [
            [
                'name' => 'Maersk Line',
                'email' => 'ops@maersk.com', // Assuming email field exists and is required/fillable?
                'code' => 'CUST001',
                'vessels' => ['Maersk Alabama', 'Maersk Kensington', 'Maersk Seletar']
            ],
            [
                'name' => 'Nakilat',
                'email' => 'fleet@nakilat.com', 
                'code' => 'CUST002',
                'vessels' => ['Al Zubara', 'Al Ghariya', 'Al Hamla']
            ],
             [
                'name' => 'Milaha',
                'email' => 'tech@milaha.com',
                'code' => 'CUST003',
                'vessels' => ['Halul 50', 'Halul 60']
            ]
        ];

        foreach ($customers as $cData) {
            $customer = Customer::firstOrCreate(
                ['company_name' => $cData['name']],
                [
                    'email' => $cData['email'],
                    'phone' => '123456789',
                    'address' => 'Doha, Qatar'
                ]
            );

            foreach ($cData['vessels'] as $vName) {
                Vessel::firstOrCreate(
                    ['name' => $vName, 'customer_id' => $customer->id],
                    [
                        'imo_number' => rand(1000000, 9999999),
                        'flag' => 'Qatar',
                        'type' => 'Container Ship'
                    ]
                );
            }
        }

        // 2. Certificate Templates
        $category = \App\Models\EquipmentCategory::first(); // Fallback
        
        $templates = [
            [
                'name' => 'Default Calibration Template',
                'title' => 'Calibration Certificate',
                'equipment_category_id' => $category?->id,
            ],
            [
                'name' => 'Default Inspection Template',
                'title' => 'Inspection Report',
                'equipment_category_id' => $category?->id,
            ]
        ];

        foreach ($templates as $tData) {
            CertificateTemplate::firstOrCreate(
                ['name' => $tData['name']],
                [
                    'title' => $tData['title'],
                    'equipment_category_id' => $tData['equipment_category_id'],
                    'is_active' => true,
                    'is_default' => true
                ]
            );
        }
    }
}
