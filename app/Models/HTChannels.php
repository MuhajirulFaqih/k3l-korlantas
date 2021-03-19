<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HTChannels extends Model
{
    protected $connection = 'mysqlht';

    public function __construct()
    {
        $this->table = env('DB_PREFIX_HT', "murmur_")."channels";
    }

    public $timestamps = false;

    protected $primaryKey = 'channel_id';

    protected $fillable = ['server_id', 'channel_id', 'parent_id', 'name', 'inheritacl', 'used_in'];

    public function scopeGetAll($query){
        $query->where('used_in', env('HT_INITIALIZATION', null));

        return $query;
    }
}
