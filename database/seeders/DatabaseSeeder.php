<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            RolePermissionSeeder::class,  // Must run before DemoUsersSeeder
            CertificateTypeSeeder::class,
            EquipmentMasterSeeder::class,
            DemoUsersSeeder::class,
            AccountsSeeder::class,
            StorekeeperSeeder::class, // Added Storekeeper Seeder
            CustomerVesselDemoSeeder::class,  // Customers, contacts, and vessels
            SampleCertificateSeeder::class,   // Sample certificates
            MarineInventorySeeder::class,     // Added Inventory Seeder
            DemoDataSeeder::class,            // Added Demo Data Seeder (Comprehensive)
        ]);
    }
}
