<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $engineerRole = Role::firstOrCreate(['slug' => 'engineer'], ['name' => 'Engineer']);
        $technicianRole = Role::firstOrCreate(['slug' => 'technician'], ['name' => 'Technician']);
        // $managerRole = Role::where('slug', 'manager')->first(); // Assuming manager exists

        // Create 3 Engineers
        for ($i = 1; $i <= 3; $i++) {
            $user = User::create([
                'name' => "Engineer $i",
                'email' => "engineer$i@louis-marine.com",
                'mobile' => '5500' . (1000 + $i),
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            $user->roles()->attach($engineerRole);
            
            $user->employeeDetail()->create([
                'employee_code' => "ENG-" . (200 + $i),
                'nationality' => 'India',
                'dob' => '1990-01-01',
                'gender' => 'Male',
                'designation' => 'Senior Engineer',
                'department' => 'Service',
                'employment_type' => 'Permanent',
                'work_location' => 'Doha',
                'status' => 'Active',
                'payment_mode' => 'Bank Transfer',
                'basic_salary' => 5000 + ($i * 500),
                'allowance_transport' => 500,
                'allowance_food' => 300,
                'hourly_rate' => 50,
                'bank_details' => [
                    'bank_name' => 'QNB',
                    'account_number' => '123456789' . $i,
                    'iban' => 'QA12QNB00000000123456' . $i
                ],
                'join_date' => now()->subMonths($i * 6),
            ]);

            // Add Dummy Document
            $user->documents()->create([
                'document_type' => 'Passport',
                'document_number' => 'P' . (12345 + $i),
                'issue_date' => now()->subYears(2),
                'expiry_date' => now()->addYears(3),
                'file_path' => 'dummy/passport.pdf', // Placeholder
            ]);
        }

        // Create 5 Technicians
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => "Technician $i",
                'email' => "tech$i@louis-marine.com",
                'mobile' => '6600' . (1000 + $i),
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            $user->roles()->attach($technicianRole);
            
            $user->employeeDetail()->create([
                'employee_code' => "TECH-" . (300 + $i),
                'nationality' => 'Philippines',
                'dob' => '1995-05-15',
                'gender' => 'Male',
                'designation' => 'Technician',
                'department' => 'Field Service',
                'employment_type' => 'Contract',
                'work_location' => 'Site A',
                'status' => 'Active',
                'payment_mode' => 'Bank Transfer',
                'basic_salary' => 2500,
                'allowance_transport' => 200,
                'allowance_food' => 200,
                'hourly_rate' => 25,
                'bank_details' => [
                    'bank_name' => 'CBQ',
                    'account_number' => '987654321' . $i,
                    'iban' => 'QA98CBQ00000000987654' . $i
                ],
                'join_date' => now()->subMonths($i),
            ]);
        }
    }
}
