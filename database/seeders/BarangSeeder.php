<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'barang_id' => 1,
                'kategori_id' => 1,
                'supplier_id' => 1,
                'barang_kode' => 'ELK001',
                'barang_nama' => 'Laptop Asus',
                'harga_beli' => 10000000,
                'harga_jual' => 12000000,
                'created_at' => now(),
            ],
            [
                'barang_id' => 2,
                'kategori_id' => 1,
                'supplier_id' => 2,
                'barang_kode' => 'ELK002',
                'barang_nama' => 'Laptop Acer',
                'harga_beli' => 12000000,
                'harga_jual' => 14000000,
                'created_at' => now(),
            ],
        ];
        DB::table('m_barang')->insert($data);
    }
}
