<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ship Owner
        $owner = \App\Models\Customer::firstOrCreate(
            ['email' => 'fleet@nakilat.com'],
            [
                'company_name' => 'Nakilat (Qatar Gas Transport)',
                'type' => 'Owner',
                'status' => 'Active',
                'cr_number' => 'CR-123456',
                'vat_number' => 'VAT-QA-001',
                'phone' => '+974 4496 8800',
                'address' => 'Al Shoumoukh Towers, C-Ring Road',
                'city' => 'Doha',
                'country' => 'Qatar',
                'notes' => 'Key Account via Nakilat.',
            ]
        );

        \App\Models\CustomerContact::firstOrCreate(
            ['email' => 'a.malki@nakilat.com'],
            [
                'customer_id' => $owner->id,
                'name' => 'Capt. Ahmed Al-Malki',
                'role' => 'Fleet Manager',
                'phone' => '+974 5555 1234',
                'is_primary' => true,
            ]
        );

        \App\Models\Vessel::firstOrCreate(
            ['imo_number' => '9337987'],
            [
                'customer_id' => $owner->id,
                'name' => 'Al Ghariya',
                'type' => 'LNG Carrier',
                'flag' => 'Marshall Islands',
                'year_built' => '2008',
                'status' => 'active',
            ]
        );

        // 2. Shipping Agent
        $agent = \App\Models\Customer::firstOrCreate(
            ['email' => 'agency@milaha.com'],
            [
                'company_name' => 'Milaha (Qatar Navigation)',
                'type' => 'Agent',
                'status' => 'Active',
                'cr_number' => 'CR-987654',
                'vat_number' => 'VAT-QA-002',
                'phone' => '+974 4494 9666',
                'address' => 'Milaha Tower, West Bay',
                'city' => 'Doha',
                'country' => 'Qatar',
            ]
        );

        \App\Models\CustomerContact::firstOrCreate(
            ['email' => 'j.doe@milaha.com'],
            [
                'customer_id' => $agent->id,
                'name' => 'John Doe',
                'role' => 'Operations Agent',
                'phone' => '+974 6666 7890',
                'is_primary' => true,
            ]
        );
        
        // 3. Operator
        $operator = \App\Models\Customer::firstOrCreate(
            ['email' => 'ops@halul.com'],
            [
                'company_name' => 'Halul Offshore Services',
                'type' => 'Operator',
                'status' => 'Active',
                'cr_number' => 'CR-555555',
                'vat_number' => 'VAT-QA-003',
                'phone' => '+974 4433 3333',
                'address' => 'Ras Laffan Industrial City',
                'city' => 'Ras Laffan',
                'country' => 'Qatar',
            ]
        );
        
         \App\Models\Vessel::firstOrCreate(
            ['imo_number' => '9609999'],
            [
                'customer_id' => $operator->id,
                'name' => 'Halul 42',
                'type' => 'Offshore Supply Ship',
                'flag' => 'Qatar',
                'year_built' => '2012',
                'status' => 'active',
            ]
        );
    }
}
