<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KesatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('pers_kesatuan')->truncate();

        $data = collect();

        $kesatuan = DB::table('kesatuan')
            ->select([
                    'nama_satuan as kesatuan', 
                    'kode_satuan', 
                    DB::raw('SUBSTR(kode_satuan, 1, 3) as kode1'), 
                    DB::raw('SUBSTR(kode_satuan, 1, 5) as kode2'), 
                    DB::raw('SUBSTR(kode_satuan, 1, 7) as kode3'), 
                    DB::raw('SUBSTR(kode_satuan, 1, 9) as kode4'), 
                    DB::raw('SUBSTR(kode_satuan, 1, 11) as kode5')
                ])
            ->whereRaw("kode_satuan like '220%'")
            ->orderBy(DB::raw('LENGTH(kode_satuan)'), 'ASC')
            ->orderBy('kode_satuan')->get();

        foreach ($kesatuan as $row){
            switch (strlen($row->kode_satuan)) {
                case 3:
                    $lv1 = $data->where('kode_satuan', $row->kode1)->first();
                    if (!$lv1)
                        $data->push((object)['kesatuan' => $row->kesatuan, 'kode_satuan' => $row->kode_satuan, 'level' => 1, 'children' => collect()]);
                    break;
                case 5:
                    $lv1 = $data->where('kode_satuan', $row->kode1)->first();
                    $collect = (object)['kesatuan' => $row->kesatuan, 'kode_satuan' => $row->kode_satuan, 'level' => 2, 'children' => collect()];
                    $lv1->children->push($collect);
                    break;
                case 7:
                    $lv1 = $data->where('kode_satuan', $row->kode1)->first();
                    $lv2 = optional(optional(optional($lv1)->children)->where('kode_satuan', $row->kode2))->first();
                    $collect = (object)['kesatuan' => $row->kesatuan, 'kode_satuan' => $row->kode_satuan, 'level' => 3,  'children' => collect()];
                    optional(optional($lv2)->children)->push($collect);
                    break;
                case 9:
                    $lv1 = $data->where('kode_satuan', $row->kode1)->first();
                    $lv2 = optional(optional(optional($lv1)->children)->where('kode_satuan', $row->kode2))->first();
                    $lv3 = optional(optional(optional($lv2)->children)->where('kode_satuan', $row->kode3))->first();
                    $collect = (object)['kesatuan' => $row->kesatuan, 'kode_satuan' => $row->kode_satuan, 'level' => 4,  'children' => collect()];
                    optional(optional($lv3)->children)->push($collect);
                    break;
                
                case 11:
                    $lv1 = $data->where('kode_satuan', $row->kode1)->first();
                    $lv2 = optional(optional(optional($lv1)->children)->where('kode_satuan', $row->kode2))->first();
                    $lv3 = optional(optional(optional($lv2)->children)->where('kode_satuan', $row->kode3))->first();
                    $lv4 = optional(optional(optional($lv3)->children)->where('kode_satuan', $row->kode4))->first();
                    optional(optional($lv4)->children)->push((object)['kesatuan' => $row->kesatuan, 'kode_satuan' => $row->kode_satuan, 'level' => 5,  'children' => collect()]);
                    break;
            }
        }

        \App\Models\Kesatuan::create(json_decode($data->toJson(), true)[0]);
    }
}
