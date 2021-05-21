<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
{
	protected $table = 'jenis';

    public function tempat()
    {
        return $this->hasMany(TempatVital::class, 'id_jenis');
    }
}
