<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PangkatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('pers_pangkat')->truncate();

        \App\Models\Pangkat::insert([
            [ "pangkat" => 'AKP', "pangkat_lengkap" => 'AJUN KOMISARIS POLISI'],
            [ "pangkat" => 'IPTU', "pangkat_lengkap" => 'INSPEKTUR POLISI SATU'],
            [ "pangkat" => 'IPDA', "pangkat_lengkap" => 'INSPEKTUR POLISI DUA'],
            [ "pangkat" => 'AIPTU', "pangkat_lengkap" => 'AJUN INSPEKTUR POLISI SATU'],
            [ "pangkat" => 'AIPDA', "pangkat_lengkap" => 'AJUN INSPEKTUR POLISI DUA'],
            [ "pangkat" => 'BRIPKA', "pangkat_lengkap" => 'BRIGADIR POLISI KEPALA'],
            [ "pangkat" => 'BRIPTU', "pangkat_lengkap" => 'BRIGADIR POLISI SATU'],
            [ "pangkat" => 'BRIPDA', "pangkat_lengkap" => 'BRIGADIR POLISI DUA'],
            [ "pangkat" => 'BRIGADIR', "pangkat_lengkap" => 'BRIGADIR POLISI'],
            [ "pangkat" => 'KOMPOL', "pangkat_lengkap" => 'KOMISARIS POLISI'],
            [ "pangkat" => 'AKBP', "pangkat_lengkap" => 'AJUN KOMISARIS BESAR POLISI'],
            [ "pangkat" => 'KOMBESPOL', "pangkat_lengkap" => 'KOMISARIS BESAR POLISI'],
            [ "pangkat" => 'BHARADA', "pangkat_lengkap" => 'BHAYANGKARA DUA'],
            [ "pangkat" => 'BHARATU', "pangkat_lengkap" => 'BHAYANGKARA SATU'],
            [ "pangkat" => 'BHARAKA', "pangkat_lengkap" => 'BHAYANGKARA KEPALA'],
            [ "pangkat" => 'KOPDA', "pangkat_lengkap" => 'KOPRAL DUA'],
            [ "pangkat" => 'KOPTU', "pangkat_lengkap" => 'KOPRAL SATU'],
            [ "pangkat" => 'KOPKA', "pangkat_lengkap" => 'KOPRAL KEPALA'],
            [ "pangkat" => 'IRJEN POL', "pangkat_lengkap" => 'INSPEKTUR JENDERAL POLISI'],
            [ "pangkat" => 'BRIGJEN POL', "pangkat_lengkap" => 'BRIGADIR JENDERAL POLISI'],
            [ "pangkat" => 'KOMJEN POL', "pangkat_lengkap" => 'KOMISARIS JENDERAL POLISI'],
            [ "pangkat" => 'PENATA', "pangkat_lengkap" => 'PENATA'],
            [ "pangkat" => 'PENATA I', "pangkat_lengkap" => 'PENATA TINGKAT I'],
            [ "pangkat" => 'PENDA', "pangkat_lengkap" => 'PENATA MUDA'],
            [ "pangkat" => 'PENDA I', "pangkat_lengkap" => 'PENATA MUDA TINGKAT I'],
            [ "pangkat" => 'PENGATUR', "pangkat_lengkap" => 'PENGATUR'],
            [ "pangkat" => 'PENGATUR I', "pangkat_lengkap" => 'PENGATUR TINGKAT I'],
            [ "pangkat" => 'PEMBINA', "pangkat_lengkap" => 'PEMBINA'],
            [ "pangkat" => 'PENGDA', "pangkat_lengkap" => 'PENGATUR MUDA'],
            [ "pangkat" => 'PENGDA I', "pangkat_lengkap" => 'PENGATUR MUDA TINGKAT I'],
            [ "pangkat" => 'JURMUD', "pangkat_lengkap" => 'JURU MUDA'],
            [ "pangkat" => 'JURU', "pangkat_lengkap" => 'JURU']
        ]);
    }
}
