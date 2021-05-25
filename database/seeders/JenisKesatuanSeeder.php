<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisKesatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('jenis_kesatuan')->truncate();
        \App\Models\JenisKesatuan::insert([
            ['jenis' => 'MABES' ],
            ['jenis' => 'SATKER MABES' ],
            ['jenis' => 'FUNGSI MABES' ],
            ['jenis' => 'POLDA' ],
            ['jenis' => 'SATKER POLDA' ],
            ['jenis' => 'SUBSATKER POLDA' ],
            ['jenis' => 'POLRES' ],
            ['jenis' => 'SATKER_POLRES' ],
            ['jenis' => 'POLSEK' ],
        ]);
    }
}
