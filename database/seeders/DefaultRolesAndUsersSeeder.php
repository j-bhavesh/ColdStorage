<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DefaultRolesAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $roles = ['super_admin', 'admin', 'accountant', 'service_manager', 'farmer'];

        foreach ($roles as $roleName) {
            // Create role if it doesn't exist
            $role = Role::firstOrCreate(['name' => $roleName]);

            // Create user for role
            $user = User::create([
                'name' => ucfirst(str_replace('_', '', $roleName)) . ' User',
                'email' => str_replace(' ', '', $roleName) . '@coldstorage.com',
                'password' => Hash::make('lloyd@321'), // Default password
            ]);

            // Assign role to user
            $user->assignRole($role);
        }

    }
}
