<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\FilterJenisPemilik;

class Plb extends Model
{
    use HasFactory, FilterJenisPemilik, UserTimezoneAware;
    protected $table = 'plb';
    protected $fillable = ['id_user', 'lat', 'lng', 'id_kesatuan', 'id_kesatuan_tujuan', 'keterangan'];

    public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }
   
    public function tujuan(){
        return $this->belongsTo(Kesatuan::class, 'id_kesatuan_tujuan');
    }

    public function scopeFilterJenisPemilik($query, $user)
    {
        return $this->jenisPemilik($query, $user);
    }

    public function scopeFiltered($query, $filter)
    {
        if($filter == null)
            return $query;

        $idBulan = array ('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
        $enBulan = array ('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $filter = str_ireplace($idBulan, $enBulan, $filter);

        return $query->whereIn('id',
            DB::table('plb as p')
                ->select('p.id')
                ->join('user as u', 'u.id', '=', 'p.id_user')
                ->leftJoin('personil as p', function ($join){
                    $join->on('p.id', '=', 'u.id_pemilik')
                        ->on('u.jenis_pemilik', '=', DB::raw('\'personil\''));
                })
                ->where(function($sub) use ($filter) {
                    $sub->where('p.nama', 'LIKE', '%'.$filter.'%')
                        ->orWhere('p.keterangan', 'LIKE', '%'.$filter.'%')
                        ->orWhere(DB::raw('DATE_FORMAT(p.created_at, "%d %M %Y")'), 'LIKE', '%'.$filter.'%');
                })
                ->get()->pluck('id')->all()
        );
    }

}
