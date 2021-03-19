<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
// use Schedula\Laravel\PassportSocialite\User\UserSocialAccount;

// class User extends Authenticatable implements UserSocialAccount
class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'user';

    protected $fillable = [
        'username', 'password', 'fcm_id', 'perangkat', 'diverifikasi', 'id_pemilik', 'jenis_pemilik', 'kode'
    ];

    protected $with = ['pemilik'];

    protected $casts = [
        'diverifikasi' => 'boolean'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function findForPassport(string $username)
    {
        return $this->where('username', $username)->first();
    }

    public function kejadian(){
        return $this->hasMany(Kejadian::class, 'id_user');
    }

    public function darurat(){
        return $this->hasMany(Darurat::class, 'id_user');
    }

    public function onGoing(){
        $darurat = $this->darurat()->where('selesai', false)->first();

        if ($darurat)
            return ['id' => $darurat->id, 'jenis' => 'darurat'];

        $kejadian = $this->kejadian()->where('selesai', false)->where('follow_me', true)->first();

        if ($kejadian)
            return ['id' => $kejadian->id, 'jenis' => 'kejadian'];

        return null;
    }

    /**
     * Find user using social provider's id
     *
     * @param string $provider Provider name as requested from oauth e.g. facebook
     * @param string $id User id of social provider
     *
     * @return User
     */
    public static function findForPassportSocialite($provider, $id)
    {
        $account = Masyarakat::where('provider', $provider)->where('provider_id', $id)->first();
        if ($account) {
            if ($account->auth)
                return $account->auth;
        }
        return null;
    }

    public function pemilik()
    {
        return $this->morphTo(null, 'jenis_pemilik', 'id_pemilik')->withTrashed();
    }

    public function getNamaAttribute($value)
    {
        switch ($this->jenis_pemilik) {
            case 'personil':
                return $this->pemilik->pangkat->pangkat . ' ' . $this->pemilik->nama;
            case 'bhabin':
                return $this->pemilik->personil->pangkat->pangkat . ' ' . $this->pemilik->personil->nama;
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
            case 'bhabin':
                return url('api/upload/personil/' . $this->pemilik->personil->nrp . '.jpg');
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
            case 'bhabin':
                return $this->pemilik->personil->jabatan->jabatan;
            case 'masyarakat':
                return "Masyarakat";
        }
        return null;
    }

    public function scopeLogedIn($query){
        $query->select('id')
            ->where(function ($q) {
                $q->where('jenis_pemilik', 'personil')
                    ->orWhere('jenis_pemilik', 'bhabin');
            })
            ->whereIn('id', DB::table('oauth_access_tokens')->select('user_id')->where(['client_id' => 2, 'revoked' => 0]));

        return $query;
    }
}
