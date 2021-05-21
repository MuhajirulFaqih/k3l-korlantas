<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogPatroli extends Model
{
    use HasFactory;
    protected $table = 'log_patroli';

    protected $fillable = ['id_status', 'lat', 'lng'];
}
