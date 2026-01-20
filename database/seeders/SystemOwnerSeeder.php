<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SystemOwnerSeeder extends Seeder
{
    public function run()
    {
        // 1. Create System Owner Role (Tech God)
        $role = Role::firstOrCreate(
            ['slug' => 'system-owner'],
            ['name' => 'System Owner']
        );

        // 2. Create System User
        $user = User::firstOrCreate(
            ['email' => 'system@louis-marine.com'],
            ['name' => 'System Owner', 'password' => Hash::make('password')]
        );

        // 3. Assign Role
        $user->roles()->syncWithoutDetaching([$role->id]);

        $this->command->info('System Owner (system@louis-marine.com) created with full technical access.');
    }
}
