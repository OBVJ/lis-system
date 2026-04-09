<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure roles exist before assigning them to seeded users.
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'doctor']);
        Role::firstOrCreate(['name' => 'receptionist']);
        Role::firstOrCreate(['name' => 'technician']);

        // Create Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@lis.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
        if(!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Create Doctor User
        $doctor = User::updateOrCreate(
            ['email' => 'doctor@lis.com'],
            [
                'name' => 'Dr. Ahmed Hassan',
                'password' => Hash::make('123456'),
                'role' => 'doctor',
                'is_active' => true,
            ]
        );
        if(!$doctor->hasRole('doctor')) {
            $doctor->assignRole('doctor');
        }

        // Create Receptionist User
        $receptionist = User::updateOrCreate(
            ['email' => 'receptionist@lis.com'],
            [
                'name' => 'Receptionist Fatima',
                'password' => Hash::make('123456'),
                'role' => 'receptionist',
                'is_active' => true,
            ]
        );
        if(!$receptionist->hasRole('receptionist')) {
            $receptionist->assignRole('receptionist');
        }

        // Create Lab Technician User
        $technician = User::updateOrCreate(
            ['email' => 'technician@lis.com'],
            [
                'name' => 'Lab Technician Omar',
                'password' => Hash::make('123456'),
                'role' => 'technician',
                'is_active' => true,
            ]
        );
        if(!$technician->hasRole('technician')) {
            $technician->assignRole('technician');
        }

        // Create Additional Test Users
        $testUsers = [
            [
                'email' => 'test1@lis.com',
                'name' => 'Test User 1',
                'role' => 'admin'
            ],
            [
                'email' => 'test2@lis.com',
                'name' => 'Test User 2',
                'role' => 'doctor'
            ],
            [
                'email' => 'test3@lis.com',
                'name' => 'Test User 3',
                'role' => 'receptionist'
            ],
            [
                'email' => 'test4@lis.com',
                'name' => 'Test User 4',
                'role' => 'technician'
            ]
        ];

        foreach ($testUsers as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                    'role' => $userData['role'],
                    'is_active' => true,
                ]
            );
            if(!$user->hasRole($userData['role'])) {
                $user->assignRole($userData['role']);
            }
        }

        // Output success message if running in console
        if (app()->runningInConsole()) {
            $this->command->info('Users created successfully!');
            $this->command->info('Login credentials:');
            $this->command->info('Admin: admin@lis.com / 123456');
            $this->command->info('Doctor: doctor@lis.com / 123456');
            $this->command->info('Receptionist: receptionist@lis.com / 123456');
            $this->command->info('Technician: technician@lis.com / 123456');
            $this->command->info('Test users: test1@lis.com to test4@lis.com / 123456');
        }
    }
}