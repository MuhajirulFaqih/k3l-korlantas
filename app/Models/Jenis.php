<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
{
    use UserTimezoneAware;

	protected $table = 'jenis';

    public function tempat()
    {
        return $this->hasMany(TempatVital::class, 'id_jenis');
    }
}
