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
                'barang_kode' => 'ELK001',
                'barang_nama' => 'Laptop Asus',
                'harga_beli' => 10000000,
                'harga_jual' => 12000000,
                'created_at' => now(),
            ],
            [
                'barang_id' => 2,
                'kategori_id' => 1,
                'barang_kode' => 'ELK002',
                'barang_nama' => 'Laptop Acer',
                'harga_beli' => 12000000,
                'harga_jual' => 14000000,
                'created_at' => now(),
            ],
            [
                'barang_id' => 3,
                'kategori_id' => 2,
                'barang_kode' => 'MKN001',
                'barang_nama' => 'Bakso',
                'harga_beli' => 5000,
                'harga_jual' => 10000,
                'created_at' => now(),
            ],
            [
                'barang_id' => 4,
                'kategori_id' => 2,
                'barang_kode' => 'MKN002',
                'barang_nama' => 'Mie Ayam',
                'harga_beli' => 7000,
                'harga_jual' => 12000,
                'created_at' => now(),
            ],
            [
                'barang_id' => 5,
                'kategori_id' => 3,
                'barang_kode' => 'PKN001',
                'barang_nama' => 'Kemeja',
                'harga_beli' => 50000,
                'harga_jual' => 70000,
                'created_at' => now(),
            ],
            [
                'barang_id' => 6,
                'kategori_id' => 3,
                'barang_kode' => 'PKN002',
                'barang_nama' => 'Celana',
                'harga_beli' => 60000,
                'harga_jual' => 80000,
                'created_at' => now(),
            ],
            [
                'barang_id' => 7,
                'kategori_id' => 4,
                'barang_kode' => 'KND001',
                'barang_nama' => 'Motor Honda',
                'harga_beli' => 15000000,
                'harga_jual' => 18000000,
                'created_at' => now(),
            ],
            [
                'barang_id' => 8,
                'kategori_id' => 4,
                'barang_kode' => 'KND002',
                'barang_nama' => 'Mobil Toyota',
                'harga_beli' => 20000000,
                'harga_jual' => 25000000,
                'created_at' => now(),
            ],
            [
                'barang_id' => 9,
                'kategori_id' => 5,
                'barang_kode' => 'PHS001',
                'barang_nama' => 'Cincin Emas',
                'harga_beli' => 500000,
                'harga_jual' => 700000,
                'created_at' => now(),
            ],
            [
                'barang_id' => 10,
                'kategori_id' => 5,
                'barang_kode' => 'PHS002',
                'barang_nama' => 'Kalung Mutiara',
                'harga_beli' => 700000,
                'harga_jual' => 1000000,
                'created_at' => now(),
            ],
        ];
        DB::table('m_barang')->insert($data);
    }
}
