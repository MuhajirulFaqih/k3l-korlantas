<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TempatVital extends Model
{
	protected $table = 'tempat_vital';

    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'id_jenis');
    }

    public function jajaran()
    {
        return $this->belongsTo(Kesatuan::class, 'id_jajaran');
    }

    function kolomHitungJarak($lat, $lng)
    {
        /*return DB::raw(
            '((ACOS(SIN('.$lat.' * PI() / 180) * SIN(lat * PI() / 180) + COS('.$lat.' * PI() / 180) * COS(lat * PI() / 180) * COS(('.$lng.' - lng) * PI() / 180)) * 180 / PI()) * 60 * 1.1515 * 1.609344) AS jarak'
        );*/
        return DB::raw(
            '(
                6371 *
                acos(
                    cos( radians( '.$lat.' ) ) *
                    cos( radians( `lat` ) ) *
                    cos(
                        radians( `lng` ) - radians( '.$lng.' )
                    ) +
                    sin(radians('.$lat.')) *
                    sin(radians(`lat`))
                )
            ) AS jarak'
        );
    }
}
