<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Vessel;
use App\Models\EquipmentCategory;
use App\Models\EquipmentItem;
use App\Models\ServiceJob;
use App\Models\JobEquipment;
use App\Models\JobAssignment;
use App\Models\Certificate;
use App\Models\CertificateType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CompleteDemoDataSeeder extends Seeder
{
    private $users = [];
    private $customers = [];
    private $contacts = [];
    private $vessels = [];
    private $equipmentItems = [];
    private $jobs = [];

    public function run()
    {
        echo "🚀 Starting Louis Marine Qatar Demo Data Seeder...\n\n";

        $this->createUsers();
        $this->createCustomers();
        $this->createCustomerContacts();
        $this->createVessels();
        $this->ensureEquipmentData();
        $this->createCertificateTypes();
        $this->createServiceJobs();
        $this->createJobAssignments();
        $this->createJobEquipment();
        $this->createCertificates();

        echo "\n✅ Demo Data Seeding Complete!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📧 LOGIN CREDENTIALS:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Super Admin:  superadmin@louismarine.qa / password\n";
        echo "Manager:      manager@louismarine.qa / password\n";
        echo "Engineer 1:   ahmed.hassan@louismarine.qa / password\n";
        echo "Engineer 2:   mohammed.ali@louismarine.qa / password\n";
        echo "Technician 1: rajesh.kumar@louismarine.qa / password\n";
        echo "Technician 2: santhosh.m@louismarine.qa / password\n";
        echo "Technician 3: abdul.rahman@louismarine.qa / password\n";
        echo "Stockist:     stock@louismarine.qa / password\n";
        echo "Accounts:     accounts@louismarine.qa / password\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }

    private function createUsers()
    {
        echo "👥 Creating Demo Users...\n";

        // Get roles
        $adminRole = Role::where('slug', 'admin')->first();
        $managerRole = Role::where('slug', 'manager')->first();
        $engineerRole = Role::where('slug', 'engineer')->first();
        $technicianRole = Role::where('slug', 'technician')->first();
        $stockistRole = Role::where('slug', 'stockist')->first();

        // Super Admin
        $this->users['admin'] = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@louismarine.qa',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);
        $this->users['admin']->roles()->attach($adminRole);

        // Manager
        $this->users['manager'] = User::create([
            'name' => 'Operations Manager',
            'email' => 'manager@louismarine.qa',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);
        $this->users['manager']->roles()->attach($managerRole);

        // Engineers
        $this->users['engineer1'] = User::create([
            'name' => 'Ahmed Hassan',
            'email' => 'ahmed.hassan@louismarine.qa',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);
        $this->users['engineer1']->roles()->attach($engineerRole);

        $this->users['engineer2'] = User::create([
            'name' => 'Mohammed Ali',
            'email' => 'mohammed.ali@louismarine.qa',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);
        $this->users['engineer2']->roles()->attach($engineerRole);

        // Technicians
        $this->users['tech1'] = User::create([
            'name' => 'Rajesh Kumar',
            'email' => 'rajesh.kumar@louismarine.qa',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);
        $this->users['tech1']->roles()->attach($technicianRole);

        $this->users['tech2'] = User::create([
            'name' => 'Santhosh M',
            'email' => 'santhosh.m@louismarine.qa',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);
        $this->users['tech2']->roles()->attach($technicianRole);

        $this->users['tech3'] = User::create([
            'name' => 'Abdul Rahman',
            'email' => 'abdul.rahman@louismarine.qa',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);
        $this->users['tech3']->roles()->attach($technicianRole);

        // Stockist
        $this->users['stockist'] = User::create([
            'name' => 'Store Incharge',
            'email' => 'stock@louismarine.qa',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);
        $this->users['stockist']->roles()->attach($stockistRole);

        // Accounts
        $this->users['accounts'] = User::create([
            'name' => 'Accounts Officer',
            'email' => 'accounts@louismarine.qa',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);

        echo "   ✓ Created 9 demo users\n";
    }

    private function createCustomers()
    {
        echo "🏢 Creating Customer Companies...\n";

        $this->customers['gulf'] = Customer::create([
            'company_name' => 'Gulf Marine Services WLL',
            'contact_person' => 'Khalid Al-Mansoori',
            'email' => 'info@gulfmarine.qa',
            'phone' => '+974 4444 5555',
            'address' => 'Building 23, Street 45, Industrial Area, Doha, Qatar',
            'city' => 'Doha',
            'state' => 'Doha',
            'country' => 'Qatar',
            'postal_code' => '00000',
            'vat_number' => 'QA-VAT-123456',
            'cr_number' => 'CR-2019-45678',
            'status' => 'active'
        ]);

        $this->customers['doha'] = Customer::create([
            'company_name' => 'Doha Offshore Logistics',
            'contact_person' => 'Saeed Al-Thani',
            'email' => 'operations@dohaoffshore.qa',
            'phone' => '+974 4455 6677',
            'address' => 'Tower 5, West Bay, Doha, Qatar',
            'city' => 'Doha',
            'state' => 'Doha',
            'country' => 'Qatar',
            'postal_code' => '00000',
            'vat_number' => 'QA-VAT-234567',
            'cr_number' => 'CR-2020-56789',
            'status' => 'active'
        ]);

        $this->customers['qatar'] = Customer::create([
            'company_name' => 'Qatar Shipping & Transport Co.',
            'contact_person' => 'Hassan Al-Kuwari',
            'email' => 'contact@qatarshipping.qa',
            'phone' => '+974 4466 7788',
            'address' => 'Port Area, Mesaieed, Qatar',
            'city' => 'Mesaieed',
            'state' => 'Al Wakrah',
            'country' => 'Qatar',
            'postal_code' => '00000',
            'vat_number' => 'QA-VAT-345678',
            'cr_number' => 'CR-2018-67890',
            'status' => 'active'
        ]);

        echo "   ✓ Created 3 customer companies\n";
    }

    private function createCustomerContacts()
    {
        echo "📇 Creating Customer Contacts...\n";

        // Gulf Marine Services Contacts
        $this->contacts['gulf_eng1'] = CustomerContact::create([
            'customer_id' => $this->customers['gulf']->id,
            'name' => 'Eng. Youssef Ibrahim',
            'designation' => 'Marine Engineer',
            'email' => 'youssef@gulfmarine.qa',
            'phone' => '+974 5551 2345',
            'contact_type' => 'Engineer',
            'can_request_job' => true,
            'can_receive_invoice' => false,
            'is_primary' => true,
            'status' => 'active'
        ]);

        $this->contacts['gulf_eng2'] = CustomerContact::create([
            'customer_id' => $this->customers['gulf']->id,
            'name' => 'Eng. Tariq Mahmoud',
            'designation' => 'Technical Manager',
            'email' => 'tariq@gulfmarine.qa',
            'phone' => '+974 5552 3456',
            'contact_type' => 'Engineer',
            'can_request_job' => true,
            'can_receive_invoice' => false,
            'is_primary' => false,
            'status' => 'active'
        ]);

        $this->contacts['gulf_accounts'] = CustomerContact::create([
            'customer_id' => $this->customers['gulf']->id,
            'name' => 'Fatima Al-Dosari',
            'designation' => 'Accounts Manager',
            'email' => 'accounts@gulfmarine.qa',
            'phone' => '+974 5553 4567',
            'contact_type' => 'Accounts',
            'can_request_job' => false,
            'can_receive_invoice' => true,
            'is_primary' => false,
            'status' => 'active'
        ]);

        // Doha Offshore Logistics Contacts
        $this->contacts['doha_eng1'] = CustomerContact::create([
            'customer_id' => $this->customers['doha']->id,
            'name' => 'Capt. Ahmed Al-Naimi',
            'designation' => 'Fleet Manager',
            'email' => 'ahmed.naimi@dohaoffshore.qa',
            'phone' => '+974 5554 5678',
            'contact_type' => 'Manager',
            'can_request_job' => true,
            'can_receive_invoice' => false,
            'is_primary' => true,
            'status' => 'active'
        ]);

        $this->contacts['doha_accounts'] = CustomerContact::create([
            'customer_id' => $this->customers['doha']->id,
            'name' => 'Mohammed Al-Sulaiti',
            'designation' => 'Finance Officer',
            'email' => 'finance@dohaoffshore.qa',
            'phone' => '+974 5555 6789',
            'contact_type' => 'Accounts',
            'can_request_job' => false,
            'can_receive_invoice' => true,
            'is_primary' => false,
            'status' => 'active'
        ]);

        // Qatar Shipping Contacts
        $this->contacts['qatar_eng1'] = CustomerContact::create([
            'customer_id' => $this->customers['qatar']->id,
            'name' => 'Eng. Ali Al-Marri',
            'designation' => 'Chief Engineer',
            'email' => 'ali.marri@qatarshipping.qa',
            'phone' => '+974 5556 7890',
            'contact_type' => 'Engineer',
            'can_request_job' => true,
            'can_receive_invoice' => false,
            'is_primary' => true,
            'status' => 'active'
        ]);

        $this->contacts['qatar_accounts'] = CustomerContact::create([
            'customer_id' => $this->customers['qatar']->id,
            'name' => 'Noor Al-Ansari',
            'designation' => 'Accounts Head',
            'email' => 'accounts@qatarshipping.qa',
            'phone' => '+974 5557 8901',
            'contact_type' => 'Accounts',
            'can_request_job' => false,
            'can_receive_invoice' => true,
            'is_primary' => false,
            'status' => 'active'
        ]);

        echo "   ✓ Created 7 customer contacts\n";
    }

    private function createVessels()
    {
        echo "🚢 Creating Vessels...\n";

        // Gulf Marine Services Vessels
        $this->vessels['doha_star'] = Vessel::create([
            'customer_id' => $this->customers['gulf']->id,
            'company_contact_id' => $this->contacts['gulf_eng1']->id,
            'name' => 'MV Doha Star',
            'imo_number' => 'IMO9234567',
            'flag' => 'Qatar',
            'vessel_type' => 'Supply Vessel',
            'gross_tonnage' => '2500',
            'year_built' => '2015',
            'status' => 'active'
        ]);

        $this->vessels['gulf_pioneer'] = Vessel::create([
            'customer_id' => $this->customers['gulf']->id,
            'company_contact_id' => $this->contacts['gulf_eng2']->id,
            'name' => 'MV Gulf Pioneer',
            'imo_number' => 'IMO9345678',
            'flag' => 'Qatar',
            'vessel_type' => 'Offshore Support',
            'gross_tonnage' => '3200',
            'year_built' => '2018',
            'status' => 'active'
        ]);

        // Doha Offshore Logistics Vessels
        $this->vessels['qatar_explorer'] = Vessel::create([
            'customer_id' => $this->customers['doha']->id,
            'company_contact_id' => $this->contacts['doha_eng1']->id,
            'name' => 'MV Qatar Explorer',
            'imo_number' => 'IMO9456789',
            'flag' => 'Qatar',
            'vessel_type' => 'Crew Boat',
            'gross_tonnage' => '1800',
            'year_built' => '2019',
            'status' => 'active'
        ]);

        // Qatar Shipping Vessels
        $this->vessels['al_rayyan'] = Vessel::create([
            'customer_id' => $this->customers['qatar']->id,
            'company_contact_id' => $this->contacts['qatar_eng1']->id,
            'name' => 'MV Al Rayyan',
            'imo_number' => 'IMO9567890',
            'flag' => 'Qatar',
            'vessel_type' => 'General Cargo',
            'gross_tonnage' => '5000',
            'year_built' => '2017',
            'status' => 'active'
        ]);

        $this->vessels['lusail'] = Vessel::create([
            'customer_id' => $this->customers['qatar']->id,
            'company_contact_id' => $this->contacts['qatar_eng1']->id,
            'name' => 'MV Lusail',
            'imo_number' => 'IMO9678901',
            'flag' => 'Qatar',
            'vessel_type' => 'Container Ship',
            'gross_tonnage' => '8500',
            'year_built' => '2020',
            'status' => 'active'
        ]);

        echo "   ✓ Created 5 vessels\n";
    }

    private function ensureEquipmentData()
    {
        echo "🔧 Ensuring Equipment Master Data...\n";

        // Check if equipment data exists, if not run the seeder
        if (EquipmentCategory::count() == 0) {
            $this->call(EquipmentMasterSeeder::class);
        }

        // Store equipment items for later use
        $this->equipmentItems['fire_co2_5kg'] = EquipmentItem::where('code', 'FE-CO2-5KG')->first();
        $this->equipmentItems['fire_dcp_9kg'] = EquipmentItem::where('code', 'FE-DCP-9KG')->first();
        $this->equipmentItems['liferaft_10p'] = EquipmentItem::where('code', 'LR-10P-DL')->first();
        $this->equipmentItems['lifejacket_adult'] = EquipmentItem::where('code', 'LJ-ADULT-SOLAS')->first();
        $this->equipmentItems['scba_30min'] = EquipmentItem::where('code', 'SCBA-30MIN')->first();

        echo "   ✓ Equipment master data ready\n";
    }

    private function createCertificateTypes()
    {
        echo "📜 Creating Certificate Types...\n";

        if (CertificateType::count() == 0) {
            CertificateType::insert([
                ['name' => 'Fire Extinguisher Service Certificate', 'validity_months' => 12, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Life Raft Service Certificate', 'validity_months' => 12, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'SCBA Service Certificate', 'validity_months' => 12, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Life Jacket Inspection Certificate', 'validity_months' => 24, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        echo "   ✓ Created certificate types\n";
    }

    private function createServiceJobs()
    {
        echo "💼 Creating Service Jobs...\n";

        // Job 1: Completed - Fire Safety Equipment Service
        $this->jobs['job1'] = ServiceJob::create([
            'job_number' => 'JOB-2025-0001',
            'customer_id' => $this->customers['gulf']->id,
            'vessel_id' => $this->vessels['doha_star']->id,
            'requested_by_contact_id' => $this->contacts['gulf_eng1']->id,
            'title' => 'Annual Fire Safety Equipment Service',
            'job_type' => 'Field Service',
            'priority' => 'High',
            'job_source' => 'Email',
            'status' => ServiceJob::STATUS_COMPLETED,
            'scheduled_at' => now()->subDays(10),
            'service_category' => 'Fire Fighting Equipment',
            'service_types' => json_encode(['Inspection', 'Servicing', 'Testing']),
            'special_instructions' => 'Vessel berthed at Hamad Port. Gate pass required.',
            'created_by' => $this->users['engineer1']->id,
            'created_at' => now()->subDays(15),
            'updated_at' => now()->subDays(5)
        ]);

        // Job 2: In Progress - Life Saving Appliances
        $this->jobs['job2'] = ServiceJob::create([
            'job_number' => 'JOB-2025-0002',
            'customer_id' => $this->customers['doha']->id,
            'vessel_id' => $this->vessels['qatar_explorer']->id,
            'requested_by_contact_id' => $this->contacts['doha_eng1']->id,
            'title' => 'Life Raft Annual Service',
            'job_type' => 'Workshop',
            'priority' => 'Medium',
            'job_source' => 'Call',
            'status' => ServiceJob::STATUS_IN_PROGRESS,
            'scheduled_at' => now()->addDays(2),
            'service_category' => 'Life Saving Appliances',
            'service_types' => json_encode(['Annual Service', 'Hydrostatic Test']),
            'special_instructions' => 'Life rafts to be collected from vessel.',
            'created_by' => $this->users['engineer2']->id,
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(1)
        ]);

        // Job 3: Completed - SCBA Service
        $this->jobs['job3'] = ServiceJob::create([
            'job_number' => 'JOB-2025-0003',
            'customer_id' => $this->customers['qatar']->id,
            'vessel_id' => $this->vessels['al_rayyan']->id,
            'requested_by_contact_id' => $this->contacts['qatar_eng1']->id,
            'title' => 'SCBA Functional Test & Service',
            'job_type' => 'Field Service',
            'priority' => 'High',
            'job_source' => 'Contract',
            'status' => ServiceJob::STATUS_COMPLETED,
            'scheduled_at' => now()->subDays(7),
            'service_category' => 'Breathing Apparatus',
            'service_types' => json_encode(['Functional Test', 'Cylinder Inspection']),
            'special_instructions' => 'Vessel at dry dock. Access via main gate.',
            'created_by' => $this->users['engineer1']->id,
            'created_at' => now()->subDays(12),
            'updated_at' => now()->subDays(3)
        ]);

        // Job 4: Closed - Life Jackets Inspection
        $this->jobs['job4'] = ServiceJob::create([
            'job_number' => 'JOB-2025-0004',
            'customer_id' => $this->customers['gulf']->id,
            'vessel_id' => $this->vessels['gulf_pioneer']->id,
            'requested_by_contact_id' => $this->contacts['gulf_eng2']->id,
            'title' => 'Life Jacket Inspection & Certification',
            'job_type' => 'Field Service',
            'priority' => 'Medium',
            'job_source' => 'Email',
            'status' => ServiceJob::STATUS_CLOSED,
            'scheduled_at' => now()->subDays(20),
            'service_category' => 'Life Saving Appliances',
            'service_types' => json_encode(['Visual Inspection', 'Buoyancy Test']),
            'special_instructions' => 'Total 50 life jackets to be inspected.',
            'created_by' => $this->users['engineer2']->id,
            'created_at' => now()->subDays(25),
            'updated_at' => now()->subDays(15)
        ]);

        // Job 5: Requested - Fire Extinguisher Refill
        $this->jobs['job5'] = ServiceJob::create([
            'job_number' => 'JOB-2025-0005',
            'customer_id' => $this->customers['qatar']->id,
            'vessel_id' => $this->vessels['lusail']->id,
            'requested_by_contact_id' => $this->contacts['qatar_eng1']->id,
            'title' => 'Fire Extinguisher Refilling Service',
            'job_type' => 'Workshop',
            'priority' => 'Low',
            'job_source' => 'Walk-in',
            'status' => ServiceJob::STATUS_REQUESTED,
            'scheduled_at' => now()->addDays(5),
            'service_category' => 'Fire Fighting Equipment',
            'service_types' => json_encode(['Refilling', 'Pressure Test']),
            'special_instructions' => '12 units to be refilled.',
            'created_by' => $this->users['engineer1']->id,
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(1)
        ]);

        echo "   ✓ Created 5 service jobs\n";
    }

    private function createJobAssignments()
    {
        echo "👷 Creating Job Assignments...\n";

        // Job 1 Assignments
        JobAssignment::create([
            'service_job_id' => $this->jobs['job1']->id,
            'user_id' => $this->users['tech1']->id,
            'assigned_by' => $this->users['engineer1']->id,
            'status' => 'completed',
            'assigned_at' => now()->subDays(10)
        ]);

        JobAssignment::create([
            'service_job_id' => $this->jobs['job1']->id,
            'user_id' => $this->users['tech2']->id,
            'assigned_by' => $this->users['engineer1']->id,
            'status' => 'completed',
            'assigned_at' => now()->subDays(10)
        ]);

        // Job 2 Assignments
        JobAssignment::create([
            'service_job_id' => $this->jobs['job2']->id,
            'user_id' => $this->users['tech3']->id,
            'assigned_by' => $this->users['engineer2']->id,
            'status' => 'assigned',
            'assigned_at' => now()->subDays(5)
        ]);

        // Job 3 Assignments
        JobAssignment::create([
            'service_job_id' => $this->jobs['job3']->id,
            'user_id' => $this->users['tech1']->id,
            'assigned_by' => $this->users['engineer1']->id,
            'status' => 'completed',
            'assigned_at' => now()->subDays(7)
        ]);

        echo "   ✓ Created job assignments\n";
    }

    private function createJobEquipment()
    {
        echo "🔧 Creating Job Equipment...\n";

        // Job 1 Equipment
        if ($this->equipmentItems['fire_co2_5kg']) {
            JobEquipment::create([
                'service_job_id' => $this->jobs['job1']->id,
                'equipment_item_id' => $this->equipmentItems['fire_co2_5kg']->id,
                'quantity' => 8,
                'condition_notes' => 'All units serviced and pressure tested',
                'technician_status' => json_encode(['serviced' => true, 'tested' => true])
            ]);
        }

        if ($this->equipmentItems['fire_dcp_9kg']) {
            JobEquipment::create([
                'service_job_id' => $this->jobs['job1']->id,
                'equipment_item_id' => $this->equipmentItems['fire_dcp_9kg']->id,
                'quantity' => 6,
                'condition_notes' => 'Refilled and tested',
                'technician_status' => json_encode(['refilled' => true, 'tested' => true])
            ]);
        }

        // Job 2 Equipment
        if ($this->equipmentItems['liferaft_10p']) {
            JobEquipment::create([
                'service_job_id' => $this->jobs['job2']->id,
                'equipment_item_id' => $this->equipmentItems['liferaft_10p']->id,
                'quantity' => 2,
                'condition_notes' => 'Annual service in progress',
                'technician_status' => json_encode(['serviced' => false])
            ]);
        }

        // Job 3 Equipment
        if ($this->equipmentItems['scba_30min']) {
            JobEquipment::create([
                'service_job_id' => $this->jobs['job3']->id,
                'equipment_item_id' => $this->equipmentItems['scba_30min']->id,
                'quantity' => 4,
                'condition_notes' => 'Functional test completed, all units operational',
                'technician_status' => json_encode(['tested' => true, 'passed' => true])
            ]);
        }

        // Job 4 Equipment
        if ($this->equipmentItems['lifejacket_adult']) {
            JobEquipment::create([
                'service_job_id' => $this->jobs['job4']->id,
                'equipment_item_id' => $this->equipmentItems['lifejacket_adult']->id,
                'quantity' => 50,
                'condition_notes' => 'All units inspected and certified',
                'technician_status' => json_encode(['inspected' => true, 'certified' => true])
            ]);
        }

        echo "   ✓ Created job equipment records\n";
    }

    private function createCertificates()
    {
        echo "📜 Creating Certificates...\n";

        $fireExtType = CertificateType::where('name', 'LIKE', '%Fire Extinguisher%')->first();
        $scbaType = CertificateType::where('name', 'LIKE', '%SCBA%')->first();
        $lifeJacketType = CertificateType::where('name', 'LIKE', '%Life Jacket%')->first();

        // Certificate for Job 1 - Fire Extinguishers
        if ($fireExtType) {
            Certificate::create([
                'certificate_type_id' => $fireExtType->id,
                'customer_id' => $this->customers['gulf']->id,
                'vessel_id' => $this->vessels['doha_star']->id,
                'service_job_id' => $this->jobs['job1']->id,
                'job_number' => $this->jobs['job1']->job_number,
                'equipment_type' => 'Fire Extinguisher CO2 5kg',
                'certificate_number' => 'LMQ-2025-FIR-0001',
                'status' => 'ISSUED',
                'issue_date' => now()->subDays(5),
                'expiry_date' => now()->addMonths(12)->subDays(5),
                'data' => json_encode([
                    'quantity' => 8,
                    'location' => 'Various locations onboard',
                    'test_pressure' => '60 bar',
                    'remarks' => 'All units tested and certified'
                ]),
                'created_by' => $this->users['engineer1']->id,
                'approved_by' => $this->users['engineer1']->id
            ]);
        }

        // Certificate for Job 3 - SCBA
        if ($scbaType) {
            Certificate::create([
                'certificate_type_id' => $scbaType->id,
                'customer_id' => $this->customers['qatar']->id,
                'vessel_id' => $this->vessels['al_rayyan']->id,
                'service_job_id' => $this->jobs['job3']->id,
                'job_number' => $this->jobs['job3']->job_number,
                'equipment_type' => 'SCBA Set 30 Minutes',
                'certificate_number' => 'LMQ-2025-SCB-0001',
                'status' => 'ISSUED',
                'issue_date' => now()->subDays(3),
                'expiry_date' => now()->addMonths(12)->subDays(3),
                'data' => json_encode([
                    'quantity' => 4,
                    'test_type' => 'Functional Test',
                    'cylinder_pressure' => '300 bar',
                    'remarks' => 'All sets tested and operational'
                ]),
                'created_by' => $this->users['engineer1']->id,
                'approved_by' => $this->users['engineer1']->id
            ]);
        }

        // Certificate for Job 4 - Life Jackets
        if ($lifeJacketType) {
            Certificate::create([
                'certificate_type_id' => $lifeJacketType->id,
                'customer_id' => $this->customers['gulf']->id,
                'vessel_id' => $this->vessels['gulf_pioneer']->id,
                'service_job_id' => $this->jobs['job4']->id,
                'job_number' => $this->jobs['job4']->job_number,
                'equipment_type' => 'Life Jacket Adult SOLAS',
                'certificate_number' => 'LMQ-2025-LIF-0001',
                'status' => 'ISSUED',
                'issue_date' => now()->subDays(15),
                'expiry_date' => now()->addMonths(24)->subDays(15),
                'data' => json_encode([
                    'quantity' => 50,
                    'inspection_type' => 'Visual & Buoyancy Test',
                    'standard' => 'SOLAS',
                    'remarks' => 'All units inspected and certified'
                ]),
                'created_by' => $this->users['engineer2']->id,
                'approved_by' => $this->users['engineer2']->id
            ]);
        }

        echo "   ✓ Created 3 certificates\n";
    }
}
