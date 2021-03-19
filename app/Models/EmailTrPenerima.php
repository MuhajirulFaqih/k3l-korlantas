<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTrPenerima extends Model
{
    protected $table = 'email_tr_penerima';

    protected $fillable = ['id_email', 'id_kesatuan', 'email'];

    public function tr(){
        return $this->belongsTo(EmailTr::class, 'id_email');
    }

    public function kesatuan(){
        return $this->belongsTo(Kesatuan::class, 'id_kesatuan');
    }
}
