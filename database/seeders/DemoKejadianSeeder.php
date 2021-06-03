<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoKejadianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('kejadian')->truncate();

        \App\Models\Kejadian::insert([
            [ 
                'id_user' => 1, 
                'id_kesatuan' => 3,
                'id_darurat' => null, 
                'w_kejadian' => \Carbon\Carbon::now(), 
                'kejadian' => 'Senpi', 
                'keterangan' => 'Demo Keterangan', 
                'lokasi' => 'Jalan Trunojoyo',
                'lat' => '-7.118737', 
                'lng' => '111.8926732', 
                'verifikasi' => '0', 
                'follow_me' => '0',
                'selesai' => null,
                'created_at' => \Carbon\Carbon::now(), 
            ],
            [ 
                'id_user' => 11, 
                'id_darurat' => null, 
                'w_kejadian' => \Carbon\Carbon::now(), 
                'kejadian' => 'Penculikan', 
                'keterangan' => 'Demo Keterangan', 
                'lokasi' => 'Jalan Trunojoyo',
                'lat' => '-7.118737', 
                'lng' => '111.8926732', 
                'verifikasi' => '0', 
                'follow_me' => '0',
                'selesai' => null,
                'created_at' => \Carbon\Carbon::now(), 
            ],
        ]);
    }
}
