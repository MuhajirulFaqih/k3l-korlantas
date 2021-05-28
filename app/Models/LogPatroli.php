<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogPatroli extends Model
{
    use HasFactory, UserTimezoneAware;
    protected $table = 'log_patroli';

    protected $fillable = ['id_status', 'lat', 'lng'];
}
