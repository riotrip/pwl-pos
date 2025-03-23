<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'supplier_id' => 1,
                'supplier_kode' => 'JPN',
                'supplier_nama' => 'Jepang',
                'created_at' => now(),
            ],
            [
                'supplier_id' => 2,
                'supplier_kode' => 'INA',
                'supplier_nama' => 'Indonesia',
                'created_at' => now(),
            ],
        ];
        DB::table('m_supplier')->insert($data);
    }
}
