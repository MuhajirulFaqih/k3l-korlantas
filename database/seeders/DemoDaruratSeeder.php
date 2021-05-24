<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDaruratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('darurat')->truncate();

        \App\Models\Darurat::insert([
            [ "id_user" => 1, "lat" => '-7.118737', "lng" => '111.8926732', 'acc' => '50', 'selesai' => 0],
        ]);
    }
}
