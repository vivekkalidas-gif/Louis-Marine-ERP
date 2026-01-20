<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;

class EngineerRoleSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Engineer Role
        $role = Role::firstOrCreate(
            ['slug' => 'engineer'],
            ['name' => 'Engineer', 'label' => 'Service Engineer']
        );

        // 2. Define Permissions
        // Engineers need specific operational permissions. 
        // Note: Access control (viewing ONLY assigned jobs) is often handled in Controller/Policy logic, 
        // but high-level 'view' permission is needed to access the module at all.
        $permissions = [
            'jobs.view', 'jobs.edit', // Edit includes status updates
            'vessels.view', // Read-only access to vessel info to perform job
            'certificates.view', 'certificates.create', 'certificates.edit', // Create/Manage certs
            'reports.create', // Service reports
        ];

        // 3. Sync Permissions
        $permissionIds = [];
        foreach ($permissions as $slug) {
            $perm = Permission::firstOrCreate(
                ['slug' => $slug],
                ['name' => Str::title(str_replace('.', ' ', $slug))]
            );
            $permissionIds[] = $perm->id;
        }

        $role->permissions()->sync($permissionIds);

        // 4. Create Demo Engineer User
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'engineer@louis-marine.com'],
            ['name' => 'Field Engineer', 'password' => \Illuminate\Support\Facades\Hash::make('password')]
        );
        $user->roles()->syncWithoutDetaching([$role->id]);
        
        // Assign a mock job to this engineer for testing restrictions
        // (Assuming ServiceJob model and factory/seeder exists, but doing simple create here if needed, 
        // though DemoDataSeeder handles job creation. I'll rely on DemoDataSeeder or existing data).
        
        $this->command->info('Engineer role and demo user created.');
    }
}
