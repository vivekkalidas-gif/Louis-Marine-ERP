<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Vessel;

class CustomerVesselDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates realistic demo data for Louis Marine Qatar:
     * - 3 Customer Companies (Qatar-based)
     * - Multiple Contacts per Customer (Engineers & Accounts)
     * - 4 Vessels assigned to Customers and Contacts
     */
    public function run(): void
    {
        echo "🏢 Creating Demo Customers...\n";

        // ============================================
        // CUSTOMER 1: Gulf Marine Services WLL
        // ============================================
        $gulfMarine = Customer::create([
            'company_name' => 'Gulf Marine Services WLL',
            'email' => 'info@gulfmarine.qa',
            'phone' => '+974 4412 3001',
            'address' => 'Building 45, Street 23, Ras Abu Aboud',
            'city' => 'Doha',
            'country' => 'Qatar',
            'vat_number' => 'QCR-100245',
            'cr_number' => 'QCR-100245',
            'status' => 'active'
        ]);
        echo "   ✓ Created: Gulf Marine Services WLL\n";

        // Gulf Marine Contacts
        $gulfEng1 = CustomerContact::create([
            'customer_id' => $gulfMarine->id,
            'name' => 'Eng. Ahmed Al-Kuwari',
            'designation' => 'Marine Engineer',
            'email' => 'ahmed.kuwari@gulfmarine.qa',
            'phone' => '+974 5551 2001',
            'contact_type' => 'Engineer',
            'can_request_job' => true,
            'can_receive_invoice' => false,
            'is_primary' => true,
            'status' => 'active'
        ]);

        $gulfEng2 = CustomerContact::create([
            'customer_id' => $gulfMarine->id,
            'name' => 'Eng. Mohammed Al-Thani',
            'designation' => 'Technical Manager',
            'email' => 'mohammed.thani@gulfmarine.qa',
            'phone' => '+974 5552 3002',
            'contact_type' => 'Engineer',
            'can_request_job' => true,
            'can_receive_invoice' => false,
            'is_primary' => false,
            'status' => 'active'
        ]);

        $gulfAccounts = CustomerContact::create([
            'customer_id' => $gulfMarine->id,
            'name' => 'Fatima Al-Dosari',
            'designation' => 'Accounts Manager',
            'email' => 'accounts@gulfmarine.qa',
            'phone' => '+974 4412 3005',
            'contact_type' => 'Accounts',
            'can_request_job' => false,
            'can_receive_invoice' => true,
            'is_primary' => false,
            'status' => 'active'
        ]);
        echo "      → Added 3 contacts (2 Engineers, 1 Accounts)\n";

        // ============================================
        // CUSTOMER 2: Doha Offshore Logistics
        // ============================================
        $dohaOffshore = Customer::create([
            'company_name' => 'Doha Offshore Logistics',
            'email' => 'contact@dohaoffshore.qa',
            'phone' => '+974 4460 8890',
            'address' => 'Plot 12, Mesaieed Industrial Area',
            'city' => 'Mesaieed',
            'country' => 'Qatar',
            'vat_number' => 'QCR-100367',
            'cr_number' => 'QCR-100367',
            'status' => 'active'
        ]);
        echo "   ✓ Created: Doha Offshore Logistics\n";

        // Doha Offshore Contacts
        $dohaEng1 = CustomerContact::create([
            'customer_id' => $dohaOffshore->id,
            'name' => 'Capt. Youssef Ibrahim',
            'designation' => 'Fleet Manager',
            'email' => 'youssef@dohaoffshore.qa',
            'phone' => '+974 5553 4001',
            'contact_type' => 'Manager',
            'can_request_job' => true,
            'can_receive_invoice' => false,
            'is_primary' => true,
            'status' => 'active'
        ]);

        $dohaEng2 = CustomerContact::create([
            'customer_id' => $dohaOffshore->id,
            'name' => 'Eng. Tariq Mahmoud',
            'designation' => 'Operations Engineer',
            'email' => 'tariq@dohaoffshore.qa',
            'phone' => '+974 5554 5002',
            'contact_type' => 'Engineer',
            'can_request_job' => true,
            'can_receive_invoice' => false,
            'is_primary' => false,
            'status' => 'active'
        ]);

        $dohaAccounts = CustomerContact::create([
            'customer_id' => $dohaOffshore->id,
            'name' => 'Noor Al-Ansari',
            'designation' => 'Finance Officer',
            'email' => 'finance@dohaoffshore.qa',
            'phone' => '+974 4460 8895',
            'contact_type' => 'Accounts',
            'can_request_job' => false,
            'can_receive_invoice' => true,
            'is_primary' => false,
            'status' => 'active'
        ]);
        echo "      → Added 3 contacts (2 Engineers, 1 Accounts)\n";

        // ============================================
        // CUSTOMER 3: Qatar Shipping & Transport Co.
        // ============================================
        $qatarShipping = Customer::create([
            'company_name' => 'Qatar Shipping & Transport Co.',
            'email' => 'admin@qatarshipping.qa',
            'phone' => '+974 4421 7705',
            'address' => 'Al Wakrah Port Area, Gate 3',
            'city' => 'Al Wakrah',
            'country' => 'Qatar',
            'vat_number' => 'QCR-100512',
            'cr_number' => 'QCR-100512',
            'status' => 'active'
        ]);
        echo "   ✓ Created: Qatar Shipping & Transport Co.\n";

        // Qatar Shipping Contacts
        $qatarEng1 = CustomerContact::create([
            'customer_id' => $qatarShipping->id,
            'name' => 'Eng. Ali Al-Marri',
            'designation' => 'Chief Engineer',
            'email' => 'ali.marri@qatarshipping.qa',
            'phone' => '+974 5555 6001',
            'contact_type' => 'Engineer',
            'can_request_job' => true,
            'can_receive_invoice' => false,
            'is_primary' => true,
            'status' => 'active'
        ]);

        $qatarEng2 = CustomerContact::create([
            'customer_id' => $qatarShipping->id,
            'name' => 'Eng. Rashid Al-Sulaiti',
            'designation' => 'Marine Superintendent',
            'email' => 'rashid@qatarshipping.qa',
            'phone' => '+974 5556 7002',
            'contact_type' => 'Engineer',
            'can_request_job' => true,
            'can_receive_invoice' => false,
            'is_primary' => false,
            'status' => 'active'
        ]);

        $qatarAccounts = CustomerContact::create([
            'customer_id' => $qatarShipping->id,
            'name' => 'Maryam Al-Kuwari',
            'designation' => 'Accounts Head',
            'email' => 'accounts@qatarshipping.qa',
            'phone' => '+974 4421 7710',
            'contact_type' => 'Accounts',
            'can_request_job' => false,
            'can_receive_invoice' => true,
            'is_primary' => false,
            'status' => 'active'
        ]);
        echo "      → Added 3 contacts (2 Engineers, 1 Accounts)\n";

        echo "\n🚢 Creating Demo Vessels...\n";

        // ============================================
        // VESSELS
        // ============================================

        // Vessel 1: MV Gulf Pioneer (Gulf Marine)
        Vessel::create([
            'customer_id' => $gulfMarine->id,
            'company_contact_id' => $gulfEng1->id,
            'name' => 'MV Gulf Pioneer',
            'imo_number' => 'IMO9321456',
            'type' => 'Supply Vessel',
            'flag' => 'Qatar',
            'call_sign' => 'A7GP',
            'year_built' => '2015',
            'status' => 'active'
        ]);
        echo "   ✓ MV Gulf Pioneer → Gulf Marine Services (Eng. Ahmed Al-Kuwari)\n";

        // Vessel 2: MV Gulf Explorer (Gulf Marine)
        Vessel::create([
            'customer_id' => $gulfMarine->id,
            'company_contact_id' => $gulfEng2->id,
            'name' => 'MV Gulf Explorer',
            'imo_number' => 'IMO9452219',
            'type' => 'Tug Boat',
            'flag' => 'Qatar',
            'call_sign' => 'A7GE',
            'year_built' => '2018',
            'status' => 'active'
        ]);
        echo "   ✓ MV Gulf Explorer → Gulf Marine Services (Eng. Mohammed Al-Thani)\n";

        // Vessel 3: MV Doha Star (Doha Offshore)
        Vessel::create([
            'customer_id' => $dohaOffshore->id,
            'company_contact_id' => $dohaEng1->id,
            'name' => 'MV Doha Star',
            'imo_number' => 'IMO9214478',
            'type' => 'Offshore Support Vessel',
            'flag' => 'Qatar',
            'call_sign' => 'A7DS',
            'year_built' => '2016',
            'status' => 'active'
        ]);
        echo "   ✓ MV Doha Star → Doha Offshore Logistics (Capt. Youssef Ibrahim)\n";

        // Vessel 4: MV Qatar Horizon (Qatar Shipping)
        Vessel::create([
            'customer_id' => $qatarShipping->id,
            'company_contact_id' => $qatarEng1->id,
            'name' => 'MV Qatar Horizon',
            'imo_number' => 'IMO9367784',
            'type' => 'Cargo Vessel',
            'flag' => 'Qatar',
            'call_sign' => 'A7QH',
            'year_built' => '2014',
            'status' => 'active'
        ]);
        echo "   ✓ MV Qatar Horizon → Qatar Shipping & Transport (Eng. Ali Al-Marri)\n";

        echo "\n✅ Demo Data Created Successfully!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📊 SUMMARY:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Customers: 3\n";
        echo "Contacts: 9 (6 Engineers, 3 Accounts)\n";
        echo "Vessels: 4\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
}
