<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        $professorRole = \App\Models\Role::where('name', 'professor')->first();
        $studentRole = \App\Models\Role::where('name', 'student')->first();

        // Admin
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@university.com',
            'password' => \Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);

        // Professor
        $profUser = \App\Models\User::create([
            'name' => 'Professor Smith',
            'email' => 'prof@university.com',
            'password' => \Hash::make('password'),
            'role_id' => $professorRole->id,
        ]);
        \App\Models\Professor::create([
            'user_id' => $profUser->id,
            'department' => 'Computer Science',
        ]);

        // Student Group
        $group = \App\Models\Group::create([
            'name' => 'GI-2',
            'level' => 'L2',
        ]);

        // Student
        $studentUser = \App\Models\User::create([
            'name' => 'Student Doe',
            'email' => 'student@university.com',
            'password' => \Hash::make('password'),
            'role_id' => $studentRole->id,
        ]);
        \App\Models\Student::create([
            'user_id' => $studentUser->id,
            'group_id' => $group->id,
            'student_number' => 'S12345',
        ]);
    }
}
