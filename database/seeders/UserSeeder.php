<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'user_type' => 'admin',
            'account_status' => 'active',
        ]);

        // Create Instructor User
        User::create([
            'name' => 'Instructor User',
            'first_name' => 'Instructor',
            'last_name' => 'User',
            'email' => 'instructor@example.com',
            'password' => Hash::make('password123'),
            'user_type' => 'instructor',
            'account_status' => 'active',
        ]);

        // Create Student User
        User::create([
            'name' => 'Student User',
            'first_name' => 'Student',
            'last_name' => 'User',
            'email' => 'student@example.com',
            'password' => Hash::make('password123'),
            'user_type' => 'student',
            'student_id' => '12345678',
            'account_status' => 'active',
        ]);
    }
}