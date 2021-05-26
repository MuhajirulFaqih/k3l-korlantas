<?php

namespace Database\Seeders;

use App\Models\KesatuanJenis;
use Illuminate\Database\Seeder;

class KesatuanJenisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jenis = [
            ['id' => 1, 'jenis' => 'MABES'],
            ['id' => 2, 'jenis' => 'SATKER MABES'],
            ['id' => 3, 'jenis' => 'SUBSATKER MABES'],
            ['id' => 4, 'jenis' => 'FUNGSI MABES'],
            ['id' => 5, 'jenis' => 'POLDA'],
            ['id' => 6, 'jenis' => 'SATKER POLDA'],
            ['id' => 7, 'jenis' => 'SUBSATKER POLDA'],
            ['id' => 8, 'jenis' => 'POLRES'],
            ['id' => 9, 'jenis' => 'SATKER POLRES'],
            ['id' => 10, 'jenis' => 'POLSEK'],
            ['id' => 11, 'jenis' => 'SATKER POLSEK'], // SATKER POLSEK
            ['id' => 12, 'jenis' => 'SUBSATKER POLSEK'],
            ['id' => 13, 'jenis' => 'SUBSATKER POLRES'],
        ];
        KesatuanJenis::insert($jenis);
    }
}
