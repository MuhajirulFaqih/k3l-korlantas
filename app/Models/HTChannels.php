<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HTChannels extends Model
{
    use HasFactory;
    protected $connection = 'mysqlht';

    protected $table = 'murmur_channels';

    public $timestamps = false;

    protected $primaryKey = 'channel_id';

    protected $fillable = ['server_id', 'channel_id', 'parent_id', 'name', 'inheritacl', 'used_in'];

    public function __construct()
    {
        $this->table = env('DB_PREFIX_TABLE_HT', 'murmur_').'channels';
    }

    public function scopeGetAll($query){
        //$query->where('used_in', env('HT_INITIALIZATION', null));

        return $query;
    }
}
