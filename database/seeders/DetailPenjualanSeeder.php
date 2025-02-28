<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailPenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];
        for ($i = 1; $i <= 30; $i++) {
            $data[] = [
                'detail_id' => $i,
                'penjualan_id' => 1,
                'barang_id' => 1,
                'harga' => 10000,
                'jumlah' => 2,
                'created_at' => now(),
            ];
        }
        DB::table('t_penjualan_detail')->insert($data);
    }
}
