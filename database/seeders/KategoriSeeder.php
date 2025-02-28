<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kategori_id' => 1,
                'kategori_kode' => 'ELK',
                'kategori_nama' => 'Elektronik',
                'created_at' => now(),
            ],
            [
                'kategori_id' => 2,
                'kategori_kode' => 'MKN',
                'kategori_nama' => 'Makanan',
                'created_at' => now(),
            ],
            [
                'kategori_id' => 3,
                'kategori_kode' => 'PKN',
                'kategori_nama' => 'Pakaian',
                'created_at' => now(),
            ],
            [
                'kategori_id' => 4,
                'kategori_kode' => 'KND',
                'kategori_nama' => 'Kendaraan',
                'created_at' => now(),
            ],
            [
                'kategori_id' => 5,
                'kategori_kode' => 'PHS',
                'kategori_nama' => 'Perhiasan',
                'created_at' => now(),
            ],
        ];
        DB::table('m_kategori')->insert($data);
    }
}
