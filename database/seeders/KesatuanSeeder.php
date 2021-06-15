<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KesatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Schema::hasTable("kesatuan")){
            DB::unprepared(file_get_contents(storage_path('/app/sql/base_kesatuan.sql')));
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('pers_kesatuan')->truncate();

        DB::connection()->disableQueryLog();

        $data = collect();

        $kesatuan = DB::table('kesatuan')
            ->select([
                'nama_satuan as kesatuan',
                'kode_satuan',
                DB::raw('SUBSTR(kode_satuan, 1, 1) as kode0'),
                DB::raw('SUBSTR(kode_satuan, 1, 3) as kode1'),
                DB::raw('SUBSTR(kode_satuan, 1, 5) as kode2'),
                DB::raw('SUBSTR(kode_satuan, 1, 7) as kode3'),
                DB::raw('SUBSTR(kode_satuan, 1, 9) as kode4'),
                DB::raw('SUBSTR(kode_satuan, 1, 11) as kode5')
            ])
            ->orderBy(DB::raw('LENGTH(kode_satuan)'), 'ASC')
            ->orderBy('kode_satuan')->get();

        $array_level_2 = [];
        foreach ($kesatuan as $row){
            switch (strlen($row->kode_satuan)) {
                case 1:
                    $jenis = 1;
                    $lv0 = $data->where('kode_satuan', 1)->first();
                    if (!$lv0)
                        $data->push((object)['kesatuan' => $row->kesatuan, 'kode_satuan' => $row->kode_satuan, 'id_jenis_kesatuan' => $jenis, 'level' => 0, 'children' => collect()]);
                    break;
                case 3:
                    $jenis = str_starts_with($row->kesatuan, 'POLDA ') ? 4 : 2;
                    $lv0 = $data->where('kode_satuan', 1)->first();
                    $collect = (object)['kesatuan' => $row->kesatuan, 'kode_satuan' => $row->kode_satuan, 'level' => 1, 'id_jenis_kesatuan' => $jenis, 'children' => collect()];
                    if ($lv0)
                        $lv0->children->push($collect);
                    break;
                case 5:
                    $lv0 = $data->where('kode_satuan', 1)->first();
                    $lv1 = optional(optional(optional($lv0)->children)->where('kode_satuan', $row->kode1))->first();
                    $array_level_2[$row->kode_satuan] = $jenis = str_starts_with($row->kesatuan, 'POLRES ') ? 7 : 5;
                    $collect = (object)['kesatuan' => $row->kesatuan, 'kode_satuan' => $row->kode_satuan, 'level' => 2, 'id_jenis_kesatuan' => $jenis, 'children' => collect()];
                    optional(optional($lv1)->children)->push($collect);
                    break;
                case 7:
                    $lv0 = $data->where('kode_satuan', 1)->first();
                    $lv1 = optional(optional(optional($lv0)->children)->where('kode_satuan', $row->kode1))->first();
                    $lv2 = optional(optional(optional($lv1)->children)->where('kode_satuan', $row->kode2))->first();
                    $jenis = isset($array_level_2[substr($row->kode_satuan, 0, 5)]) ? ($array_level_2[substr($row->kode_satuan, 0, 5)] == 7 ? (str_starts_with($row->kesatuan, 'POLSEK ') ? 9 : 8) : 6) : null;
                    $collect = (object)['kesatuan' => $row->kesatuan, 'kode_satuan' => $row->kode_satuan, 'level' => 3, 'id_jenis_kesatuan' => $jenis, 'children' => collect()];
                    optional(optional($lv2)->children)->push($collect);
                    break;
                case 9:
                    $jenis = isset($array_level_2[substr($row->kode_satuan, 0, 5)]) ? ($array_level_2[substr($row->kode_satuan, 0, 5)] == 7 ? (str_starts_with($row->kesatuan, 'POLSEK ') ? 9 : 8) : 6) : null;
                    $lv0 = $data->where('kode_satuan', 1)->first();
                    $lv1 = optional(optional(optional($lv0)->children)->where('kode_satuan', $row->kode1))->first();
                    $lv2 = optional(optional(optional($lv1)->children)->where('kode_satuan', $row->kode2))->first();
                    $lv3 = optional(optional(optional($lv2)->children)->where('kode_satuan', $row->kode3))->first();
                    $collect = (object)['kesatuan' => $row->kesatuan, 'kode_satuan' => $row->kode_satuan, 'level' => 4, 'id_jenis_kesatuan' => $jenis, 'children' => collect()];
                    optional(optional($lv3)->children)->push($collect);
                    break;

                case 11:
                    $jenis = isset($array_level_2[substr($row->kode_satuan, 0, 5)]) ? ($array_level_2[substr($row->kode_satuan, 0, 5)] == 7 ? (str_starts_with($row->kesatuan, 'POLSEK ') ? 9 : 8) : 6) : null;
                    $lv0 = $data->where('kode_satuan', 1)->first();
                    $lv1 = optional(optional(optional($lv0)->children)->where('kode_satuan', $row->kode1))->first();
                    $lv2 = optional(optional(optional($lv1)->children)->where('kode_satuan', $row->kode2))->first();    
                    $lv3 = optional(optional(optional($lv2)->children)->where('kode_satuan', $row->kode3))->first();
                    $lv4 = optional(optional(optional($lv3)->children)->where('kode_satuan', $row->kode4))->first();
                    optional(optional($lv4)->children)->push((object)['kesatuan' => $row->kesatuan, 'kode_satuan' => $row->kode_satuan, 'level' => 5, 'id_jenis_kesatuan' => $jenis, 'children' => collect()]);
                    break;
            }
        }
        
        \App\Models\Kesatuan::create(json_decode($data->toJson(), true)[0]);
    }
}
