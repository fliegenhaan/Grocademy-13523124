<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->delete();

        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Grocademy',
            'username' => 'admin',
            'email' => 'admin@grocademy.com',
            'password' => Hash::make('password123'),
            'balance' => 1000000,
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        User::factory(10)->create();
    }
}