<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Memasukkan data role dasar
        DB::table('roles')->insert([
            [
                'id'         => 1,
                'name'       => 'admin',
                'deleted'    => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 2,
                'name'       => 'user',
                'deleted'    => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}