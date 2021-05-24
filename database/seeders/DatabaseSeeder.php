<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AgamaSeeder::class);
        $this->call(DinasSeeder::class);
        $this->call(PangkatSeeder::class);
        $this->call(JabatanSeeder::class);
        $this->call(WilayahSeeder::class);
        $this->call(KesatuanSeeder::class);
        $this->call(JenisTempatVitalSeeder::class);
        $this->call(TempatVitalSeeder::class);
        $this->call(JenisKegiatanKesatuanSeeder::class);
        $this->call(DemoUserSeeder::class);
        $this->call(DemoKegiatanSeeder::class);
        $this->call(DemoDaruratSeeder::class);
        $this->call(DemoKejadianSeeder::class);
        $this->call(DemoNewsSeeder::class);
    }
}
