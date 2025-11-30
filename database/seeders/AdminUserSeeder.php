<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if not exists
        User::firstOrCreate(
            ['email' => 'admin@ohmygoa.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('Admin@123'),
                'role' => UserRole::ADMIN,
                'is_verified' => true,
                'is_active' => true,
                'verified_at' => now(),
                'email_verified_at' => now(),
                'phone' => '9999999999',
                'city' => 'Goa',
                'state' => 'Goa',
                'country' => 'India',
            ]
        );

        // Create test business user
        User::firstOrCreate(
            ['email' => 'business@ohmygoa.com'],
            [
                'name' => 'Business User',
                'password' => Hash::make('Business@123'),
                'role' => UserRole::BUSINESS,
                'is_verified' => true,
                'is_active' => true,
                'verified_at' => now(),
                'email_verified_at' => now(),
                'phone' => '8888888888',
                'city' => 'Panaji',
                'state' => 'Goa',
                'country' => 'India',
            ]
        );

        // Create test free user
        User::firstOrCreate(
            ['email' => 'user@ohmygoa.com'],
            [
                'name' => 'Free User',
                'password' => Hash::make('User@123'),
                'role' => UserRole::FREE,
                'is_verified' => true,
                'is_active' => true,
                'verified_at' => now(),
                'email_verified_at' => now(),
                'phone' => '7777777777',
                'city' => 'Margao',
                'state' => 'Goa',
                'country' => 'India',
            ]
        );

        $this->command->info('Admin and test users created successfully!');
        $this->command->info('Admin: admin@ohmygoa.com / Admin@123');
        $this->command->info('Business: business@ohmygoa.com / Business@123');
        $this->command->info('Free User: user@ohmygoa.com / User@123');
    }
}
