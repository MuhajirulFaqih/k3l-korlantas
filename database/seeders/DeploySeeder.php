<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeploySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        DB::table('absensi_personil')->truncate();
        DB::table('admin')->truncate();
        DB::table('call_log')->truncate();
        DB::table('call_participant')->truncate();
        DB::table('informasi')->truncate();
        DB::table('kegiatan_jenis_kegiatan')->truncate();
        DB::table('komentar')->truncate();
        DB::table('log_masyarakat')->truncate();
        DB::table('log_patroli')->truncate();
        DB::table('log_status_personil')->truncate();
        DB::table('masyarakat')->truncate();
        DB::table('pengaduan')->truncate();
        DB::table('pengumuman')->truncate();
        DB::table('personil_terdekat')->truncate();
        DB::table('tindak_lanjut')->truncate();

        DB::table('kegiatan')->truncate();
        DB::table('pers_pangkat')->truncate();
        DB::table('pers_jabatan')->truncate();
        DB::table('personil')->truncate();
        DB::table('kegiatan')->truncate();
        DB::table('darurat')->truncate();
        DB::table('kejadian')->truncate();
        DB::table('news')->truncate();

        $this->call(AgamaSeeder::class);
        $this->call(DinasSeeder::class);
        $this->call(WilayahSeeder::class);
        $this->call(JenisKesatuanSeeder::class);
        $this->call(KesatuanSeeder::class);
        $this->call(UserAdminKesatuanSeeder::class);
        $this->call(JenisTempatVitalSeeder::class);
        $this->call(TempatVitalSeeder::class);
        $this->call(JenisKegiatanKesatuanSeeder::class);
        $this->call(PengaturanSeeder::class);
    }
}
