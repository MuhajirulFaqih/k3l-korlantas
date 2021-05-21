<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared(file_get_contents(storage_path('/app/sql/base_kesatuan.sql')));
        DB::unprepared(file_get_contents(storage_path('/app/sql/wil.sql')));
    }
}
