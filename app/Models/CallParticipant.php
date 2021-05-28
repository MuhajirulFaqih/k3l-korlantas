<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallParticipant extends Model
{
    use HasFactory, UserTimezoneAware;

    protected $table = 'call_participant';
    protected $guarded = [];

    public function user(){
        $this->belongsTo(User::class, 'id_user');
    }
}
