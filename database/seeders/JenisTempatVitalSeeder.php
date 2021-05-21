<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisTempatVitalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('jenis')->truncate();
        \App\Models\Jenis::insert([
            ['jenis' => 'Pos Awal', 'icon' => 'jenis/pos-awal.png'],
            ['jenis' => 'Pos Akhir', 'icon' => 'jenis/pos-akhir.png'],
            ['jenis' => 'Kantor Polisi', 'icon' => 'jenis/kantor-polisi.png'],
            ['jenis' => 'Kompleks Perumahan', 'icon' => 'jenis/kompleks-perumahan.png'],
            ['jenis' => 'Pos Polisi', 'icon' => 'jenis/pos-polisi.png'],
            ['jenis' => 'Layanan Samsat', 'icon' => 'jenis/layanan-samsat.png'],
            ['jenis' => 'Layanan SIM', 'icon' => 'jenis/layanan-sim.png'],
            ['jenis' => 'Rumah Sakit/Klinik', 'icon' => 'jenis/rumahsakit.png'],
            ['jenis' => 'Bank/Lembaga Keuangan', 'icon' => 'jenis/bank.png'],
            ['jenis' => 'Cafe/Restoran', 'icon' => 'jenis/cafe.png'],
            ['jenis' => 'Hotel/Apartemen', 'icon' => 'jenis/hotel.png'],
            ['jenis' => 'Instansi Pemerintah', 'icon' => 'jenis/instansi.png'],
            ['jenis' => 'Mal/Pusat Perbelanjaan', 'icon' => 'jenis/mall.png'],
            ['jenis' => 'Objek Vital', 'icon' => 'jenis/objek-vital.png'],
            ['jenis' => 'Pabrik/Gudang', 'icon' => 'jenis/pabrik.png'],
            ['jenis' => 'Pasar Modern', 'icon' => 'jenis/market.png'],
            ['jenis' => 'Pasar Tradisional', 'icon' => 'jenis/pasar-tradisional.png'],
            ['jenis' => 'Sekolah Menengah', 'icon' => 'jenis/sekolah-menengah.png'],
            ['jenis' => 'SPBU', 'icon' => 'jenis/spbu.png'],
            ['jenis' => 'Stasiun', 'icon' => 'jenis/stasiun.png'],
            ['jenis' => 'Tempat Hiburan', 'icon' => 'jenis/tempat-hiburan.png'],
            ['jenis' => 'Tempat Ibadah', 'icon' => 'jenis/tempat-ibadah.png'],
            ['jenis' => 'Tempat Usaha', 'icon' => 'jenis/tempat-usaha.png-'],
            ['jenis' => 'Terminal Angkutan', 'icon' => 'jenis/terminal.png'],
            ['jenis' => 'Toko/Minimarket', 'icon' => 'jenis/toko.png'],
            ['jenis' => 'Perguruan Tinggi', 'icon' => 'jenis/perguruan-tinggi.png'],
            ['jenis' => 'Lainnya', 'icon' => 'jenis/lainnya.png'],
        ]);
    }
}
