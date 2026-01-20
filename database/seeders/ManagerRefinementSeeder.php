<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ManagerRefinementSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            // 1. Create/Get Manager Role
            $managerRole = Role::firstOrCreate(
                ['slug' => 'manager'],
                ['name' => 'Manager']
            );

            // 2. Define Allowed Permissions (Full Ops, NO CMS/System/Security)
            $allowedPermissions = [
                // 1. Users & Staff (Engineers, Technicians)
                'users.view', 'users.create', 'users.edit', 'users.delete', 'users.manage',
                'roles.view', // View only, cannot change permissions
                
                // 2. Customers
                'customers.view', 'customers.create', 'customers.edit', 'customers.delete', 'customers.manage',
                
                // 3. Service Jobs & Vessels
                'jobs.view', 'jobs.create', 'jobs.edit', 'jobs.delete', 'jobs.manage',
                'vessels.view', 'vessels.create', 'vessels.edit', 'vessels.manage',
                
                // 4. Inventory & Store
                'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.adjust', 'inventory.manage',
                'suppliers.manage',
                
                // 5. Finance (Invoices, Quotes, Payments)
                'finance.view', 'invoices.manage', 'quotations.manage', 'payments.manage', 
                'sales.manage', 'purchases.manage',
                'accounts.view', 'accounts.create', 'accounts.edit', // Core accounting
                
                // 6. Certificates
                'certificates.view', 'certificates.create', 'certificates.issue', 'certificates.manage',
                
                // 7. Reports (Operational only)
                'reports.view', 'reports.finance', 'reports.operations', 'reports.inventory'
            ];

            // EXPLICITLY EXCLUDED:
            // - cms.manage
            // - settings.manage
            // - backups.manage
            // - security.manage
            // - permissions.manage

            // 3. Sync Permissions
            // Create permissions if they don't exist
            $permissionIds = [];
            foreach ($allowedPermissions as $slug) {
                $perm = Permission::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => ucfirst(str_replace('.', ' ', $slug))]
                );
                $permissionIds[] = $perm->id;
            }
            
            // Sync strictly to allowed list (removes any system perms if they were there)
            $managerRole->permissions()->sync($permissionIds);
            
            $this->command->info('Manager Role Permissions Synced.');

            // 4. Create Demo Manager User
            $user = User::firstOrCreate(
                ['email' => 'manager@louis-marine.com'],
                ['name' => 'Operational Manager', 'password' => Hash::make('password')]
            );
            
            if (!$user->hasRole('manager')) {
                $user->roles()->syncWithoutDetaching([$managerRole->id]);
            }

            $this->command->info('Manager User (manager@louis-marine.com) Ready.');
        });
    }
}
