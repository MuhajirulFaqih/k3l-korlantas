<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, UserTimezoneAware;

    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'password', 'fcm_id', 'perangkat', 'diverifikasi', 'id_pemilik', 'jenis_pemilik', 'kode', 'timezone', 'online'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function findForPassport(string $username){
        return $this->where('username', $username)->first();
    }

    public function kejadian(){
        return $this->hasMany(Kejadian::class, 'id_user');
    }

    public function darurat(){
        return $this->hasMany(Darurat::class, 'id_user');
    }

    public static function findForPassportSocialite($provider, $id){
        $account = Masyarakat::where('provider', $provider)->where('provider_id', $id)->first();
        if ($account && $account->auth){
            return $account->auth;
        }

        return null;
    }

    public function pemilik(){
        return $this->morphTo(null, 'jenis_pemilik', 'id_pemilik');
    }

    public function getNamaAttribute($value){
        switch ($this->jenis_pemilik){
            case 'personil':
                return $this->pemilik->pangkat->pangkat. ' '. $this->pemilik->nama;
            case 'masyarakat':
                return $this->pemilik->nama;
        }

        return null;
    }

    public function getFotoAttribute($value)
    {
        switch ($this->jenis_pemilik) {
            case 'personil':
                return url('api/upload/personil/' . $this->pemilik->nrp . '.jpg');
            case 'masyarakat':
                return $this->pemilik->foto ? url('api/upload/' . $this->pemilik->foto) : null;
        }
        return null;
    }

    public function getJabatanAttribute($value)
    {
        switch ($this->jenis_pemilik) {
            case 'personil':
                return $this->pemilik->jabatan->jabatan;
            case 'masyarakat':
                return "Masyarakat";
        }
        return null;
    }
}
