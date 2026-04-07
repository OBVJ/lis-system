<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Create permissions if they don't exist
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

// Create roles
$adminRole = Role::firstOrCreate(['name' => 'admin']);
$adminRole->syncPermissions(Permission::all());

$doctorRole = Role::firstOrCreate(['name' => 'doctor']);
$doctorRole->syncPermissions(['view_dashboard', 'manage_patients', 'manage_requests', 'manage_results', 'view_reports']);

$receptionistRole = Role::firstOrCreate(['name' => 'receptionist']);
$receptionistRole->syncPermissions(['view_dashboard', 'manage_patients', 'manage_requests', 'view_reports']);

$technicianRole = Role::firstOrCreate(['name' => 'technician']);
$technicianRole->syncPermissions(['view_dashboard', 'manage_requests', 'manage_samples', 'manage_results', 'view_reports']);

// Create users
$users = [
    [
        'email' => 'admin@lis.com',
        'name' => 'System Admin',
        'password' => Hash::make('123456'),
        'role' => 'admin',
    ],
    [
        'email' => 'doctor@lis.com',
        'name' => 'Dr. Ahmed Hassan',
        'password' => Hash::make('123456'),
        'role' => 'doctor',
    ],
    [
        'email' => 'receptionist@lis.com',
        'name' => 'Receptionist Fatima',
        'password' => Hash::make('123456'),
        'role' => 'receptionist',
    ],
    [
        'email' => 'technician@lis.com',
        'name' => 'Lab Technician Omar',
        'password' => Hash::make('123456'),
        'role' => 'technician',
    ],
    [
        'email' => 'test1@lis.com',
        'name' => 'Test User 1',
        'password' => Hash::make('123456'),
        'role' => 'admin',
    ],
    [
        'email' => 'test2@lis.com',
        'name' => 'Test User 2',
        'password' => Hash::make('123456'),
        'role' => 'doctor',
    ],
    [
        'email' => 'test3@lis.com',
        'name' => 'Test User 3',
        'password' => Hash::make('123456'),
        'role' => 'receptionist',
    ],
    [
        'email' => 'test4@lis.com',
        'name' => 'Test User 4',
        'password' => Hash::make('123456'),
        'role' => 'technician',
    ],
];

foreach ($users as $userData) {
    $user = User::firstOrCreate(['email' => $userData['email']], [
        'name' => $userData['name'],
        'password' => $userData['password'],
        'role' => $userData['role'],
        'is_active' => true,
    ]);

    if(!$user->hasRole($userData['role'])) {
        $user->assignRole($userData['role']);
    }
}

echo "Users created successfully!\n";
echo "Login credentials:\n";
echo "Admin: admin@lis.com / 123456\n";
echo "Doctor: doctor@lis.com / 123456\n";
echo "Receptionist: receptionist@lis.com / 123456\n";
echo "Technician: technician@lis.com / 123456\n";
echo "Test users: test1@lis.com to test4@lis.com / 123456\n";