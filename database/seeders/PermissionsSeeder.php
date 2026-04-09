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

        // Grant specific permissions to doctor role
        $doctorRole = Role::firstOrCreate(['name' => 'doctor']);
        $doctorPermissions = [
            'view_dashboard',
            'manage_patients',
            'manage_requests',
            'manage_samples',
            'manage_results',
            'view_reports'
        ];
        $doctorRole->syncPermissions($doctorPermissions);

        // Grant specific permissions to receptionist role
        $receptionistRole = Role::firstOrCreate(['name' => 'receptionist']);
        $receptionistPermissions = [
            'view_dashboard',
            'manage_patients',
            'manage_requests',
            'manage_samples',
            'view_reports'
        ];
        $receptionistRole->syncPermissions($receptionistPermissions);

        // Grant specific permissions to technician role
        $technicianRole = Role::firstOrCreate(['name' => 'technician']);
        $technicianPermissions = [
            'view_dashboard',
            'manage_requests',
            'manage_samples',
            'manage_results',
            'manage_inventory'
        ];
        $technicianRole->syncPermissions($technicianPermissions);
    }
}
