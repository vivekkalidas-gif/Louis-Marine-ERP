<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceJob;
use App\Models\Customer;
use App\Models\Vessel;
use App\Models\CustomerContact;
use App\Models\User;

class DemoJobsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates demo service jobs for Louis Marine Qatar
     */
    public function run(): void
    {
        echo "🔧 Creating Demo Service Jobs...\n";

        // Get demo data
        $customers = Customer::all();
        $vessels = Vessel::all();
        $engineers = User::whereHas('roles', fn($q) => $q->where('slug', 'engineer'))->get();
        
        if ($customers->isEmpty() || $vessels->isEmpty() || $engineers->isEmpty()) {
            echo "❌ Error: Please run CustomerVesselDemoSeeder and DemoUsersSeeder first!\n";
            return;
        }

        // Job 1: Lifeboat Service - Gulf Marine
        $gulfMarine = $customers->where('company_name', 'Gulf Marine Services WLL')->first();
        $gulfPioneer = $vessels->where('name', 'MV Gulf Pioneer')->first();
        $contact = CustomerContact::where('customer_id', $gulfMarine->id)->where('can_request_job', true)->first();
        
        ServiceJob::create([
            'job_number' => 'LMQ-' . date('Y') . '-001',
            'title' => 'Lifeboat Annual Service & Inspection',
            'customer_id' => $gulfMarine->id,
            'vessel_id' => $gulfPioneer->id,
            'requested_by_contact_id' => $contact->id,
            'job_type' => 'Field Service',
            'status' => 'in-progress',
            'priority' => 'Normal',
            'scheduled_at' => now()->addDays(2),
            'location_details' => 'Doha Port, Berth 3',
            'job_source' => 'Email',
            'assigned_engineer_id' => $engineers->random()->id,
            'created_by' => $engineers->first()->id,
        ]);
        echo "   ✓ Created: Lifeboat Service - MV Gulf Pioneer\n";

        // Job 2: Fire Fighting Equipment - Gulf Marine
        $gulfExplorer = $vessels->where('name', 'MV Gulf Explorer')->first();
        
        ServiceJob::create([
            'job_number' => 'LMQ-' . date('Y') . '-002',
            'title' => 'Fire Fighting Equipment Maintenance',
            'customer_id' => $gulfMarine->id,
            'vessel_id' => $gulfExplorer->id,
            'requested_by_contact_id' => $contact->id,
            'job_type' => 'Field Service',
            'status' => 'requested',
            'priority' => 'High',
            'scheduled_at' => now()->addDays(1),
            'location_details' => 'Doha Port, Berth 5',
            'job_source' => 'Call',
            'assigned_engineer_id' => $engineers->random()->id,
            'created_by' => $engineers->first()->id,
        ]);
        echo "   ✓ Created: Fire Fighting Service - MV Gulf Explorer\n";

        // Job 3: Life Raft Service - Doha Offshore
        $dohaOffshore = $customers->where('company_name', 'Doha Offshore Logistics')->first();
        $dohaStar = $vessels->where('name', 'MV Doha Star')->first();
        $dohaContact = CustomerContact::where('customer_id', $dohaOffshore->id)->where('can_request_job', true)->first();
        
        ServiceJob::create([
            'job_number' => 'LMQ-' . date('Y') . '-003',
            'title' => 'Life Raft Annual Servicing',
            'customer_id' => $dohaOffshore->id,
            'vessel_id' => $dohaStar->id,
            'requested_by_contact_id' => $dohaContact->id,
            'job_type' => 'Workshop Service',
            'status' => 'completed',
            'priority' => 'Normal',
            'scheduled_at' => now()->subDays(5),
            'location_details' => 'Louis Marine Workshop',
            'job_source' => 'Contract',
            'assigned_engineer_id' => $engineers->random()->id,
            'created_by' => $engineers->skip(1)->first()->id,
        ]);
        echo "   ✓ Created: Life Raft Service - MV Doha Star\n";

        // Job 4: SCBA Equipment - Qatar Shipping
        $qatarShipping = $customers->where('company_name', 'Qatar Shipping & Transport Co.')->first();
        $qatarHorizon = $vessels->where('name', 'MV Qatar Horizon')->first();
        $qatarContact = CustomerContact::where('customer_id', $qatarShipping->id)->where('can_request_job', true)->first();
        
        ServiceJob::create([
            'job_number' => 'LMQ-' . date('Y') . '-004',
            'title' => 'SCBA Equipment Testing & Certification',
            'customer_id' => $qatarShipping->id,
            'vessel_id' => $qatarHorizon->id,
            'requested_by_contact_id' => $qatarContact->id,
            'job_type' => 'Field Service',
            'status' => 'in-progress',
            'priority' => 'Emergency',
            'scheduled_at' => now(),
            'location_details' => 'Al Wakrah Port',
            'job_source' => 'Walk-in',
            'assigned_engineer_id' => $engineers->random()->id,
            'created_by' => $engineers->last()->id,
        ]);
        echo "   ✓ Created: SCBA Testing - MV Qatar Horizon\n";

        // Job 5: Immersion Suit Inspection - Gulf Marine
        ServiceJob::create([
            'job_number' => 'LMQ-' . date('Y') . '-005',
            'title' => 'Immersion Suit Inspection',
            'customer_id' => $gulfMarine->id,
            'vessel_id' => $gulfPioneer->id,
            'requested_by_contact_id' => $contact->id,
            'job_type' => 'Field Service',
            'status' => 'completed',
            'priority' => 'Low',
            'scheduled_at' => now()->subDays(10),
            'location_details' => 'Doha Port',
            'job_source' => 'Email',
            'assigned_engineer_id' => $engineers->first()->id,
            'created_by' => $engineers->first()->id,
        ]);
        echo "   ✓ Created: Immersion Suit Inspection - MV Gulf Pioneer\n";

        echo "\n✅ Demo Jobs Created Successfully!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📊 SUMMARY:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Total Jobs: 5\n";
        echo "In Progress: 2\n";
        echo "Completed: 2\n";
        echo "Requested: 1\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
}
