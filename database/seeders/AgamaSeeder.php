<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgamaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('agama')->truncate();
        \App\Models\Agama::insert([
            [
                'agama'  => 'Islam',
                'status' => '1',
            ],
            [
                'agama'  => 'Kristen Protestan',
                'status' => '1',
            ],
            [
                'agama'  => 'Kristen Katholik',
                'status' => '1',
            ],
            [
                'agama'  => 'Hindu',
                'status' => '1',
            ],
            [
                'agama'  => 'Budha',
                'status' => '1',
            ],
            [
                'agama'  => 'Konghucu',
                'status' => '1',
            ],
        ]);
    }
}
