<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTrAttachment extends Model
{
    protected $table = 'email_tr_attachment';

    protected $fillable = ['id_email', 'file', 'format'];

    public function tr(){
        return $this->belongsTo(EmailTr::class, 'id_email');
    }
}
