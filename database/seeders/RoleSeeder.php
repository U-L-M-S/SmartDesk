<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Full system access with all permissions',
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Manage teams, tickets, and documents',
            ],
            [
                'name' => 'Mitarbeiter',
                'slug' => 'mitarbeiter',
                'description' => 'Basic access to create tickets and view documents',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
