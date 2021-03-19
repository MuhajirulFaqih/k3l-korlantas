<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dinas extends Model
{
    protected $table = "dinas";
    protected $fillable = ["kegiatan"];

    public function scopeGetByStatus($query, $pimpinan = false){
        if (!$pimpinan) {
            $query->whereNotIn('kegiatan', ['PIMPINAN']);
        } else {
            $query->whereIn('kegiatan', ['PIMPINAN', 'LEPAS DINAS']);
        }

        return $query;
    }
}
