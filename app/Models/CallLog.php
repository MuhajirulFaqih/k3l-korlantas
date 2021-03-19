<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class CallLog extends Model
{
	//use Searchable;

    protected $table = 'log_call';

    protected $fillable = ['to', 'from', 'id_from', 'id_to', 'end', 'start'];

    public function fromData(){
        return $this->belongsTo(User::class, 'id_from');
    }

    public function toData(){
        return $this->belongsTo(User::class, 'id_to');
    }

    /*public function toSearchableArray()
    {
        $array = $this->toArray();
        $array['from'] = $this->fromData->pemilik['nama'];
        $array['to'] = $this->toData->pemilik['nama'];
        $array['start'] = \Carbon\Carbon::parse($this->start)->format('d F Y');
        $array['end'] = \Carbon\Carbon::parse($this->end)->format('d F Y');
        return $array;
    }*/
}
