<?php

namespace Database\Seeders;

use App\Models\Personil;
use App\Models\Masyarakat;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('personil')->truncate();
        DB::table('masyarakat')->truncate();
        DB::table('admin')->truncate();
        DB::table('user')->truncate();

        $password = bcrypt('telabangmandau2021');

        $kesatuan = [3, 124, 189, 430, 431, 434, 435, 581, 607, 2396];
        $jabatan = [3, 741, 5, 604, 2394, 2202, 2159, 2262, 2269, 3266];
        foreach ($kesatuan as $key => $kesatuan_value) {
            $personil = Personil::create([
                'nrp' => '1111111'.$key,
                'nama' => 'AKUN DEMO PERSONIL',
                'id_pangkat' => 2,
                'id_jabatan' => $jabatan[$key],
                'id_kesatuan' => $kesatuan_value,
                'kelamin' => 'L',
                'alamat' => 'KALIMANTAN TIMUR',
                'status_dinas' => 1,
                'w_status_dinas' => \Carbon\Carbon::now(),
                'bearing' => NULL,
                'no_telp' => '08888888888'.$key,
                'lat' => -7.1519363,
                'lng' => 111.8787191,
                'ptt_ht' => NULL,
            ]);
            $personil->auth()->create(['username' => $personil->nrp, 'password' => $password]);
        }

        $masyarakat = Masyarakat::create([
            'nik' => 1111111111111111,
            'nama' => 'AKUN DEMO MASYARAKAT',
            'foto' => '',
            'alamat' => 'KALIMANTAN TIMUR',
            'no_telp' => '080000000000',
            'provider' => NULL,
            'provider_id' => NULL,
        ]);
        $masyarakat->auth()->create(['username' => 'demomasyarakat', 'password' => $password]);

        $admin = Admin::create([
            'nama' => 'AKUN DEMO ADMIN',
            'status' => 0,
            'in_call' => 0,
            'visiblility' => 0,
        ]);
        $admin->auth()->create(['username' => 'demoadmin', 'password' => $password]);
    }
}
