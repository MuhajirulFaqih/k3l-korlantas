<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Kesatuan;
use Illuminate\Database\Seeder;

class UserAdminKesatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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
