<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Ticket permissions
            ['name' => 'View Tickets', 'slug' => 'tickets.view', 'module' => 'tickets'],
            ['name' => 'Create Tickets', 'slug' => 'tickets.create', 'module' => 'tickets'],
            ['name' => 'Edit Tickets', 'slug' => 'tickets.edit', 'module' => 'tickets'],
            ['name' => 'Delete Tickets', 'slug' => 'tickets.delete', 'module' => 'tickets'],
            ['name' => 'Assign Tickets', 'slug' => 'tickets.assign', 'module' => 'tickets'],
            ['name' => 'Close Tickets', 'slug' => 'tickets.close', 'module' => 'tickets'],

            // Document permissions
            ['name' => 'View Documents', 'slug' => 'documents.view', 'module' => 'documents'],
            ['name' => 'Upload Documents', 'slug' => 'documents.upload', 'module' => 'documents'],
            ['name' => 'Edit Documents', 'slug' => 'documents.edit', 'module' => 'documents'],
            ['name' => 'Delete Documents', 'slug' => 'documents.delete', 'module' => 'documents'],
            ['name' => 'Share Documents', 'slug' => 'documents.share', 'module' => 'documents'],
            ['name' => 'Manage Document Versions', 'slug' => 'documents.versions', 'module' => 'documents'],

            // User management permissions
            ['name' => 'View Users', 'slug' => 'users.view', 'module' => 'users'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'module' => 'users'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'module' => 'users'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'module' => 'users'],
            ['name' => 'Assign Roles', 'slug' => 'users.assign-roles', 'module' => 'users'],

            // Notification permissions
            ['name' => 'View Notifications', 'slug' => 'notifications.view', 'module' => 'notifications'],
            ['name' => 'Send Notifications', 'slug' => 'notifications.send', 'module' => 'notifications'],
            ['name' => 'Delete Notifications', 'slug' => 'notifications.delete', 'module' => 'notifications'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Assign permissions to roles
        $admin = Role::where('slug', 'admin')->first();
        $manager = Role::where('slug', 'manager')->first();
        $mitarbeiter = Role::where('slug', 'mitarbeiter')->first();

        // Admin gets all permissions
        $allPermissions = Permission::all();
        $admin->permissions()->attach($allPermissions);

        // Manager permissions
        $managerPermissions = Permission::whereIn('slug', [
            'tickets.view', 'tickets.create', 'tickets.edit', 'tickets.assign', 'tickets.close',
            'documents.view', 'documents.upload', 'documents.edit', 'documents.share', 'documents.versions',
            'users.view',
            'notifications.view', 'notifications.send',
        ])->get();
        $manager->permissions()->attach($managerPermissions);

        // Mitarbeiter (Employee) permissions
        $mitarbeiterPermissions = Permission::whereIn('slug', [
            'tickets.view', 'tickets.create',
            'documents.view', 'documents.upload',
            'notifications.view',
        ])->get();
        $mitarbeiter->permissions()->attach($mitarbeiterPermissions);
    }
}
