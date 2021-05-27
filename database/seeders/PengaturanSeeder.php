<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengaturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('pengaturan')->truncate();
        \App\Models\Pengaturan::insert([
            [
                "nama" => "default_password",
                "nilai" => "telabangmandau2021",
                "autoload" => 0,
            ],
            [
                "nama" => "default_banner_grid",
                "nilai" => "",
                "autoload" => 0,
            ],
            [
                "nama" => "default_mumble_channel",
                "nilai" => "0",
                "autoload" => 0,
            ],
            [
                "nama" => "auto_send_notification",
                "nilai" => "0",
                "autoload" => 0,
            ],
        ]);
    }
}
