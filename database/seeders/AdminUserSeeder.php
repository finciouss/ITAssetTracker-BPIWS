<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@itassettracker.local'],
            [
                'name'       => 'admin',
                'full_name'  => 'System Administrator',
                'password'   => Hash::make('AdminP@ssw0rd!'),
                'department' => 'IT',
                'email_verified_at' => now(),
            ]
        );

        if (!$admin->hasRole('Admin')) {
            $admin->assignRole('Admin');
        }
    }
}
