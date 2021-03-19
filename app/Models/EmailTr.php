<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmailTr extends Model
{
    protected $table = 'email_tr';

    protected $fillable = ['nomor', 'pengirim', 'w_email', 'id_email', 'sent_at'];

    public function attachment(){
        return $this->hasMany(EmailTrAttachment::class, 'id_email');
    }

    public function penerima(){
        return $this->hasMany(EmailTrPenerima::class, 'id_email');
    }

    public function scopeFilter($query, $id_kesatuan){
        $query->whereIn('id',
            DB::table('email_tr_penerima')
                ->select('id_email')
                ->where('id_kesatuan', $id_kesatuan)
                ->get()
                ->pluck('id_email')->all()
        );

        $query->orderBy('w_email', 'desc');

        return $query;
    }
}
