<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // NOTE: updateOrCreate makes this seeder safe to re-run on deployed DBs.
        // Password values are plain text here; the User model casts `password` => 'hashed'.

        // Admin
        User::updateOrCreate(
            ['email' => 'salcedojomel6@gmail.com'],
            [
                'name' => 'Admin User',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => 'Admin@12345',
                'user_type' => 'admin',
                'account_status' => 'active',
                'email_verified_at' => $now,
            ]
        );

        // Instructor
        User::updateOrCreate(
            ['email' => 'instructor@example.com'],
            [
                'name' => 'Instructor User',
                'first_name' => 'Instructor',
                'last_name' => 'User',
                'password' => 'password123',
                'user_type' => 'instructor',
                'account_status' => 'active',
                'email_verified_at' => $now,
            ]
        );

        // Student
        User::updateOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Student User',
                'first_name' => 'Student',
                'last_name' => 'User',
                'password' => 'password123',
                'user_type' => 'student',
                'student_id' => '20260001',
                'year_level' => '1st',
                'yearlevel' => '1st Year',
                'account_status' => 'active',
                'email_verified_at' => $now,
            ]
        );
    }
}
