<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DinasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('dinas')->truncate();
        \App\Models\Dinas::insert([
            [ 'kegiatan'  => 'Piket Jaga', 'icon' => NULL, 'image' => NULL ],
            [ 'kegiatan'  => 'Patroli', 'icon' => 'pin-personil/patroli.png', 'image' => NULL ],
            [ 'kegiatan'  => 'Pengawalan', 'icon' => 'pin-personil/pengawalan.png', 'image' => NULL ],
            [ 'kegiatan'  => 'Binluh', 'icon' => NULL, 'image' => NULL ],
            [ 'kegiatan'  => 'Pam dan Gatur Giat Masyarakat', 'icon' => NULL, 'image' => NULL ],
            [ 'kegiatan'  => 'Protab Pagi', 'icon' => NULL, 'image' => NULL ],
            [ 'kegiatan'  => 'Protab Malam', 'icon' => NULL, 'image' => NULL ],
            [ 'kegiatan'  => 'PIMPINAN', 'icon' => NULL, 'image' => NULL ],
            [ 'kegiatan'  => 'LEPAS DINAS', 'icon' => NULL, 'image' => NULL ],
        ]);
    }
}
