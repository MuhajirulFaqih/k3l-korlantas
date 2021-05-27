<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Kesatuan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserAdminKesatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('user')->truncate();

        $admin = Admin::create([
            'nama' => 'Synergics Digital'
        ]);
        $admin->auth()->create(['username' => 'sudo', 'password' => bcrypt('Synergics 2021')]);
        $kesatuan = Kesatuan::whereIn('level', [1, 2])->get();

        foreach ($kesatuan as $row){
            $username = str_replace(' ', '_', strtolower($row->kesatuan));
            $row->auth()->create(['username' => $username, 'password' => bcrypt('PoldaKaltim21')]);
        }
    }
}
