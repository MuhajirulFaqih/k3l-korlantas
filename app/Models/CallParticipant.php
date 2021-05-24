<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallParticipant extends Model
{
    use HasFactory;

    protected $table = 'call_participant';
    protected $guarded = [];

    public function user(){
        $this->belongsTo(User::class, 'id_user');
    }
}
