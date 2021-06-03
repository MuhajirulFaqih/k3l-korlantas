<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Seeder;

class DemoKegiatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('kegiatan')->truncate();
        DB::table('kegiatan_jenis_kegiatan')->truncate();

        $kegiatan = \App\Models\Kegiatan::create([
            'id_user' => 1,
            'id_kesatuan' => 3,
            'daftar_rekan' => null,
            'nomor_polisi' => null,
            'detail' => 'Detail kegiatan',
            'rute_patroli' => null,
            'waktu_kegiatan' => \Carbon\Carbon::now(),
            'dokumentasi' => '',
            'lat' => '-7.118737', 
            'lng' => '111.8926732',
            'id_kelurahan_binmas' => null,
            'is_quick_response' => false
        ]);
        
        $id_jenis = [215, 216, 220, 227];
        foreach($id_jenis as $keyIdJenis => $valueIdJenis) {
            \App\Models\KegiatanJenisKegiatan::create(['id_kegiatan' => $kegiatan->id, 'id_jenis_kegiatan' => $valueIdJenis]);
        }
    }
}
