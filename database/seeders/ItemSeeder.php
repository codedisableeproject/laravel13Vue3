<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('items')->insert([
            [
                'kode' => 'ITM-001',
                'nama' => 'Kopi Arabica',
                'image' => null,
                'harga' => 25000,
                'deleted' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'ITM-002',
                'nama' => 'Kopi Robusta',
                'image' => null,
                'harga' => 20000,
                'deleted' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'ITM-003',
                'nama' => 'Teh Hijau',
                'image' => null,
                'harga' => 15000,
                'deleted' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'ITM-004',
                'nama' => 'Teh Hitam',
                'image' => null,
                'harga' => 12000,
                'deleted' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
