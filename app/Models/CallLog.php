<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallLog extends Model
{
    use HasFactory;

    protected $table = 'call_log';
    protected $guarded = [];

    protected $dates = ['endTime', 'startTime'];

    public function participants(){
        return $this->hasMany(CallParticipant::class, 'id_call');
    }

    public function fromData(){
        return $this->belongsTo(User::class, 'id_from');
    }

    public function toData(){
        return $this->belongsTo(User::class, 'id_to');
    }
}
