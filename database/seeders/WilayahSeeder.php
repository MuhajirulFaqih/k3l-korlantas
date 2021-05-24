<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!Schema::hasTable('kesatuan'))
            DB::unprepared(file_get_contents(storage_path('/app/sql/base_kesatuan.sql')));

        if (!Schema::hasTable('wil_provinsi'))
            DB::unprepared(file_get_contents(storage_path('/app/sql/wil.sql')));
    }
}
