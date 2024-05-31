<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if the user already exists to avoid duplication
        $user = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('12345678')
            ]
        );

        // Check if the role already exists to avoid duplication
        $role = Role::firstOrCreate(['name' => 'Admin']);

        // Ensure permissions are seeded in the database
        $permissions = Permission::all();

        // Sync all permissions to the role
        $role->syncPermissions($permissions);

        // Assign the role to the user if not already assigned
        if (!$user->hasRole('Admin')) {
            $user->assignRole('Admin');
        }
    }

}
