<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Certificate;
use App\Models\CertificateItem;
use App\Models\CertificateType;
use App\Models\Customer;
use App\Models\Vessel;
use App\Models\ServiceJob;
use Carbon\Carbon;

class SampleCertificateSeeder extends Seeder
{
    public function run()
    {
        // 1. Fetch Dependencies
        // 1. Fetch Dependencies
        $type = CertificateType::first() ?? CertificateType::create(['name' => 'Fire Extinguisher Certificate', 'slug' => 'FE-01']);
        $customer = Customer::first() ?? Customer::create(['company_name' => 'Sample Shipping Co', 'email' => 'test@sample.com']);
        $vessel = Vessel::first() ?? Vessel::create(['name' => 'MV Sample', 'imo_number' => '1234567', 'customer_id' => $customer->id]);
        $category = \App\Models\EquipmentCategory::first() ?? \App\Models\EquipmentCategory::create(['name' => 'Fire Extinguishers', 'is_active' => true]);
        $user = \App\Models\User::first() ?? \App\Models\User::factory()->create();

        // Create Dummy Service Job
        $job = ServiceJob::first() ?? ServiceJob::create([
             'customer_id' => $customer->id,
             'vessel_id' => $vessel->id,
             'job_number' => 'JOB-' . rand(1000, 9999),
             'title' => 'Annual Fire Extinguisher Inspection',
             'status' => 'PENDING'
        ]);

        // 2. Create DRAFT Certificate
        $draft = Certificate::create([
            'certificate_type_id' => $type->id,
            'customer_id' => $customer->id,
            'vessel_id' => $vessel->id,
            'equipment_category_id' => $category->id,
            'service_job_id' => $job->id,
            'created_by' => $user->id,
            'status' => 'DRAFT',
        ]);

        // Add Items
        CertificateItem::create([
            'certificate_id' => $draft->id,
            'equipment_type' => 'Dry Powder 6kg',
            'serial_number' => 'DP-1001',
            'manufacturer' => 'Kidde',
            'manufacture_date' => '2023-01-15',
            'last_service_date' => '2023-12-15',
            'next_due_date' => '2024-12-15',
            'condition' => 'good',
            'details' => ['Location' => 'Bridge'],
        ]);
        
        CertificateItem::create([
            'certificate_id' => $draft->id,
            'equipment_type' => 'CO2 5kg',
            'serial_number' => 'CO2-2002',
            'manufacturer' => 'Eversafe',
            'manufacture_date' => '2022-05-10',
            'last_service_date' => '2023-12-15',
            'next_due_date' => '2024-12-15',
            'condition' => 'repair',
            'details' => ['Location' => 'Engine Room'],
        ]);

        // 3. Create ISSUED Certificate
        // Create another service job for the issued certificate
        $job2 = ServiceJob::create([
             'customer_id' => $customer->id,
             'vessel_id' => $vessel->id,
             'job_number' => 'JOB-' . rand(1000, 9999),
             'title' => 'Life Raft Inspection',
             'status' => 'COMPLETED'
        ]);

        $issued = Certificate::create([
            'certificate_type_id' => $type->id,
            'customer_id' => $customer->id,
            'vessel_id' => $vessel->id,
            'service_job_id' => $job2->id,
            'equipment_category_id' => $category->id,
            'status' => 'ISSUED',
            'issue_date' => Carbon::today(),
            'expiry_date' => Carbon::today()->addYear(),
            'certificate_number' => 'CERT-' . rand(1000, 9999),
            'created_by' => $user->id,
            'issued_by' => $user->id,
        ]);

         CertificateItem::create([
            'certificate_id' => $issued->id,
            'equipment_type' => 'Life Raft 25 Pax',
            'serial_number' => 'LR-5005',
            'manufacturer' => 'Viking',
            'manufacture_date' => '2021-06-20',
            'last_service_date' => '2023-12-01',
            'next_due_date' => '2024-12-01',
            'condition' => 'good',
            'details' => ['Capacity' => '25 Persons'],
        ]);

        $this->command->info('Sample Certificates Created: 1 Draft, 1 Issued.');
    }
}
