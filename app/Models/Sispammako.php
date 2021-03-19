<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sispammako extends Model
{
    protected $table = 'sispammako';

    protected $fillable = ['id_user', 'arahan', 'dokumen', 'jenis'];

    public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }

    public function scopeFiltered($query){
        $query->orderBy('created_at', 'desc');

        return $query;
    }
}
