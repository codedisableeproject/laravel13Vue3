<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            // 1. Akun Admin (role_id = 1)
            [
                'name'       => 'Admin Ganteng',
                'email'      => 'admin@example.com',
                'role_id'    => 1, // Mengarah ke role 'admin'
                'password'   => Hash::make('password'),
                'deleted'    => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 2. Akun User Biasa (role_id = 2)
            [
                'name'       => 'Budi User',
                'email'      => 'user@example.com',
                'role_id'    => 2, // Mengarah ke role 'user'
                'password'   => Hash::make('password'),
                'deleted'    => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}