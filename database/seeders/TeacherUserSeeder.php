<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TeacherUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create teacher user
        $teacher = User::create([
            'name' => 'Mario',
            'surname' => 'Rossi',
            'email' => 'teacher@sta.com',
            'password' => Hash::make('password'),
            'status' => 'active',
            'date_of_birth' => '1985-05-15',
            'gender' => 'male',
            'phone' => '+39 333 1234567',
            'address' => 'Via Roma 123, Milano, Italy',
        ]);

        // Assign teacher role
        $teacher->assignRole('teacher');

        $this->command->info('Teacher user created successfully!');
        $this->command->info('Email: teacher@sta.com');
        $this->command->info('Password: password');
    }
}
