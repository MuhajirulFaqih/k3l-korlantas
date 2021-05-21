<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallLog extends Model
{
    use HasFactory;

    protected $table = 'call_log';
    protected $fillable = ['to', 'from', 'id_from', 'id_to', 'end', 'start'];

    protected $dates = ['end', 'start'];

    public function fromData(){
        return $this->belongsTo(User::class, 'id_from');
    }

    public function toData(){
        return $this->belongsTo(User::class, 'id_to');
    }
}
