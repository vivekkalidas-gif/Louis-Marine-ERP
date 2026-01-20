<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class EngineerRefinementSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            // 1. Get Engineer Role
            $role = Role::firstOrCreate(['slug' => 'engineer'], ['name' => 'Engineer']);

            // 2. Define Permissions for Self-Sufficiency
            $perms = [
                // Job Management
                'jobs.view', 'jobs.create', 'jobs.edit', // Can create/edit own jobs
                'jobs.reports.create', 'jobs.reports.edit',
                
                // Technician Assignment
                'assignments.view', 'assignments.manage', 'assignments.log_hours',
                
                // Materials
                'materials.view', 'materials.request', // Can request, NOT manage inventory stock
                
                // Customers
                'customers.view', 'customers.create', 'customers.edit',
                
                // Vessels
                'vessels.view', 'vessels.create', 'vessels.edit',
                
                // Documents
                'certificates.view', 'certificates.create', 'certificates.issue', // Direct issue
                'delivery_notes.view', 'delivery_notes.create', 'delivery_notes.print',
                
                // Core
                'dashboard.engineer', 'profile.manage'
            ];

            // 3. Sync
            $ids = [];
            foreach ($perms as $slug) {
                // Ensure permission exists in DB
                $p = Permission::firstOrCreate(['slug' => $slug], ['name' => ucfirst(str_replace('.', ' ', $slug))]);
                $ids[] = $p->id;
            }

            // Sync, but merge with existing if needed. Using sync to be definitive for this refinement.
            // Note: If you want to keep existing, use syncWithoutDetaching, but 'sync' ensures exact set for this role scope.
            // Let's assume we WANT to add these to whatever they have, or reset. 
            // Better to syncWithoutDetaching to avoid breaking existing basic access if any.
            // Actually, let's just use syncWithoutDetaching for safety.
            $role->permissions()->syncWithoutDetaching($ids);
            
            $this->command->info('Engineer Permissions Updated for Self-Sufficiency.');
        });
    }
}
