<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create system permissions
        $permissions = [
            'view_dashboard',
            'manage_patients',
            'manage_tests',
            'manage_requests',
            'manage_samples',
            'manage_results',
            'manage_inventory',
            'view_reports',
            'manage_users',
            'manage_settings',
            'manage_roles'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Grant all permissions to admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        // You might want to assign specific things to doctor, receptionist, but for now we seeded permissions.
    }
}
