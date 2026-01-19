<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use App\Models\Device;
use App\Models\Shift;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Shifts
        $shiftDay = Shift::create([
            'name' => 'Day Shift (08:00 - 17:00)',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
        ]);

        // 2. Create Users & Roles
        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@face.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $hr = User::create([
            'name' => 'HR Manager',
            'email' => 'hr@face.com',
            'password' => Hash::make('password'),
            'role' => 'hr',
        ]);

        // 3. Create Employees (Some with login, some without)
        
        // Employee 1: มี User Login
        $empUser = User::create([
            'name' => 'John Doe',
            'email' => 'john@face.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
        ]);
        
        Employee::create([
            'user_id' => $empUser->id,
            'employee_code' => 'EMP001',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'department' => 'IT',
            'position' => 'Developer',
        ]);

        // Employee 2: ไม่มี User Login (พนักงานไลน์ผลิต ฯลฯ)
        Employee::create([
            'employee_code' => 'EMP002',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'department' => 'Sales',
            'position' => 'Sales Executive',
        ]);

        // 4. Create Devices
        Device::create([
            'name' => 'Main Entrance',
            'device_code' => 'DEV-001',
            'location' => 'Lobby',
            'ip_address' => '192.168.1.101',
            'api_token' => \Illuminate\Support\Str::random(32),
        ]);

        Device::create([
            'name' => 'Back Door',
            'device_code' => 'DEV-002',
            'location' => 'Parking Lot',
            'ip_address' => '192.168.1.102',
            'api_token' => \Illuminate\Support\Str::random(32),
        ]);
    }
}