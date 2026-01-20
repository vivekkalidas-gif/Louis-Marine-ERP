<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ChatUsersSeeder extends Seeder
{
    public function run()
    {
        $password = Hash::make('password');

        $users = [
            // Engineers
            ['name' => 'Engineer 1', 'email' => 'engineer1@louismarine.qa', 'role_slug' => 'engineer', 'role_name' => 'Engineer'],
            ['name' => 'Engineer 2', 'email' => 'engineer2@louismarine.qa', 'role_slug' => 'engineer', 'role_name' => 'Engineer'],
            ['name' => 'Engineer 3', 'email' => 'engineer3@louismarine.qa', 'role_slug' => 'engineer', 'role_name' => 'Engineer'],
            
            // Technicians
            ['name' => 'Technician 1', 'email' => 'technician1@louismarine.qa', 'role_slug' => 'technician', 'role_name' => 'Technician'],
            ['name' => 'Technician 2', 'email' => 'technician2@louismarine.qa', 'role_slug' => 'technician', 'role_name' => 'Technician'],
            ['name' => 'Technician 3', 'email' => 'technician3@louismarine.qa', 'role_slug' => 'technician', 'role_name' => 'Technician'],
            ['name' => 'Technician 4', 'email' => 'technician4@louismarine.qa', 'role_slug' => 'technician', 'role_name' => 'Technician'],
            ['name' => 'Technician 5', 'email' => 'technician5@louismarine.qa', 'role_slug' => 'technician', 'role_name' => 'Technician'],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $password,
                ]
            );

            // Create or find the role
            $role = \App\Models\Role::firstOrCreate(
                ['slug' => $userData['role_slug']],
                ['name' => $userData['role_name']]
            );

            // Assign role if not already assigned
            if (!$user->roles()->where('slug', $userData['role_slug'])->exists()) {
                $user->roles()->attach($role);
            }
        }
    }
}
