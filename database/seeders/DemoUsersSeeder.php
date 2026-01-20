<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates clean demo users for Louis Marine Qatar:
     * - 3 Engineers (Engineer 1, Engineer 2, Engineer 3)
     * - 5 Technicians (Technician 1 to Technician 5)
     */
    public function run(): void
    {
        echo "🧹 Cleaning up old demo users...\n";
        
        // Delete old demo users completely (including role assignments)
        $oldEmails = [
            'ahmed.hassan@louismarine.qa',
            'mohammed.ali@louismarine.qa',
            'rajesh.kumar@louismarine.qa',
            'santhosh.m@louismarine.qa',
            'abdul.rahman@louismarine.qa',
            'superadmin@louismarine.qa',
            'manager@louismarine.qa',
            'stock@louismarine.qa',

            'engineer@louismarine.com'
        ];
        
        User::whereIn('email', $oldEmails)->delete();

        echo "💰 Creating Accountant...\n";

        // Get or Create Accountant Role
        $accountantRole = Role::firstOrCreate(
            ['slug' => 'accountant'],
            ['name' => 'Accountant']
        );

        $accountantEmail = 'accounts@louismarine.qa';
        $accountant = User::withTrashed()->where('email', $accountantEmail)->first();

        if ($accountant) {
            if ($accountant->trashed()) {
                $accountant->restore();
            }
            $accountant->update([
                'name' => 'Accountant',
                'password' => Hash::make('password'),
                'email_verified_at' => now()
            ]);
        } else {
            $accountant = User::create([
                'name' => 'Accountant',
                'email' => $accountantEmail,
                'password' => Hash::make('password'),
                'email_verified_at' => now()
            ]);
        }
        $accountant->roles()->sync([$accountantRole->id]);
        echo "   ✓ Created/Updated Accountant\n";

        echo "👷 Creating Engineers...\n";
        
        // Get Engineer Role
        $engineerRole = Role::where('slug', 'engineer')->first();
        
        if (!$engineerRole) {
            echo "❌ Error: Engineer role not found!\n";
            return;
        }

        // Create 3 Engineers
        for ($i = 1; $i <= 3; $i++) {
            $email = "engineer{$i}@louismarine.qa";
            
            $engineer = User::withTrashed()->where('email', $email)->first();
            
            if ($engineer) {
                if ($engineer->trashed()) {
                    $engineer->restore();
                }
                $engineer->update([
                    'name' => "Engineer {$i}",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now()
                ]);
            } else {
                $engineer = User::create([
                    'name' => "Engineer {$i}",
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now()
                ]);
            }
            
            // Sync roles
            $engineer->roles()->sync([$engineerRole->id]);
            
            echo "   ✓ Created/Updated Engineer {$i}\n";
        }

        echo "🔧 Creating Technicians...\n";
        
        // Get Technician Role
        $technicianRole = Role::firstOrCreate(
            ['slug' => 'technician'],
            ['name' => 'Technician']
        );
        
        if (!$technicianRole) {
            echo "❌ Error: Technician role not found!\n";
            return;
        }

        // Create 5 Technicians
        for ($i = 1; $i <= 5; $i++) {
            $email = "technician{$i}@louismarine.qa";
            
            $technician = User::withTrashed()->where('email', $email)->first();
            
            if ($technician) {
                if ($technician->trashed()) {
                    $technician->restore();
                }
                $technician->update([
                    'name' => "Technician {$i}",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now()
                ]);
            } else {
                $technician = User::create([
                    'name' => "Technician {$i}",
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now()
                ]);
            }
            
            // Sync roles
            $technician->roles()->sync([$technicianRole->id]);
            
            echo "   ✓ Created/Updated Technician {$i}\n";
        }

        echo "\n✅ Demo Users Created Successfully!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📧 DEMO LOGIN CREDENTIALS:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "ACCOUNTS:\n";
        echo "  Accountant: accounts@louismarine.qa / password\n";
        echo "\nENGINEERS:\n";
        echo "  Engineer 1: engineer1@louismarine.qa / password\n";
        echo "  Engineer 2: engineer2@louismarine.qa / password\n";
        echo "  Engineer 3: engineer3@louismarine.qa / password\n";
        echo "\nTECHNICIANS:\n";
        echo "  Technician 1: technician1@louismarine.qa / password\n";
        echo "  Technician 2: technician2@louismarine.qa / password\n";
        echo "  Technician 3: technician3@louismarine.qa / password\n";
        echo "  Technician 4: technician4@louismarine.qa / password\n";
        echo "  Technician 5: technician5@louismarine.qa / password\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
}
