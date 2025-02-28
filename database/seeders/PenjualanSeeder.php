<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'penjualan_id' => 1,
                'user_id' => 1,
                'pembeli' => 'Budi',
                'penjualan_kode' => 'P001',
                'penjualan_tanggal' => '2021-01-01',
                'created_at' => now(),
            ],
            [
                'penjualan_id' => 2,
                'user_id' => 1,
                'pembeli' => 'Ani',
                'penjualan_kode' => 'P002',
                'penjualan_tanggal' => '2021-01-02',
                'created_at' => now(),
            ],
            [
                'penjualan_id' => 3,
                'user_id' => 1,
                'pembeli' => 'Citra',
                'penjualan_kode' => 'P003',
                'penjualan_tanggal' => '2021-01-03',
                'created_at' => now(),
            ],
            [
                'penjualan_id' => 4,
                'user_id' => 1,
                'pembeli' => 'Dewi',
                'penjualan_kode' => 'P004',
                'penjualan_tanggal' => '2021-01-04',
                'created_at' => now(),
            ],
            [
                'penjualan_id' => 5,
                'user_id' => 1,
                'pembeli' => 'Eka',
                'penjualan_kode' => 'P005',
                'penjualan_tanggal' => '2021-01-05',
                'created_at' => now(),
            ],
            [
                'penjualan_id' => 6,
                'user_id' => 1,
                'pembeli' => 'Fajar',
                'penjualan_kode' => 'P006',
                'penjualan_tanggal' => '2021-01-06',
                'created_at' => now(),
            ],
            [
                'penjualan_id' => 7,
                'user_id' => 1,
                'pembeli' => 'Gita',
                'penjualan_kode' => 'P007',
                'penjualan_tanggal' => '2021-01-07',
                'created_at' => now(),
            ],
            [
                'penjualan_id' => 8,
                'user_id' => 1,
                'pembeli' => 'Hani',
                'penjualan_kode' => 'P008',
                'penjualan_tanggal' => '2021-01-08',
                'created_at' => now(),
            ],
            [
                'penjualan_id' => 9,
                'user_id' => 1,
                'pembeli' => 'Indah',
                'penjualan_kode' => 'P009',
                'penjualan_tanggal' => '2021-01-09',
                'created_at' => now(),
            ],
            [
                'penjualan_id' => 10,
                'user_id' => 1,
                'pembeli' => 'Joko',
                'penjualan_kode' => 'P010',
                'penjualan_tanggal' => '2021-01-10',
                'created_at' => now(),
            ],
        ];
        DB::table('t_penjualan')->insert($data);
    }
}
