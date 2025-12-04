<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed roles and permissions
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);

        // Create demo admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@smartdesk.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Create demo manager user
        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@smartdesk.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $manager->assignRole('manager');

        // Create demo employee user
        $employee = User::create([
            'name' => 'Employee User',
            'email' => 'employee@smartdesk.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $employee->assignRole('mitarbeiter');
    }
}
