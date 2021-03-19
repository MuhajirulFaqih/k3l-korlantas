<?php

namespace App\Http\Controllers;

use App\Models\Bhabin;
use App\Models\Kegiatan;
use App\Models\Kejadian;
use App\Models\Komentar;
use App\Models\Masyarakat;
use App\Models\Personil;
use App\Models\TindakLanjut;
use App\Models\User;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use LaravelFCM\Message\OptionsPriorities;
use Route;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $personil, $bhabin, $masyarakat, $kegiatan, $komentar, $tindaklanjut;

    public function __construct()
    {
        $this->personil = new Personil();
        $this->bhabin = new Bhabin();
        $this->masyarakat = new Masyarakat();
        $this->komentar = new Komentar();
        $this->kegiatan = new Kegiatan();
        $this->tindaklanjut = new TindakLanjut();
    }


    public static function userAccessTokenId($access_token){
        $auth_header = explode(' ', $access_token);
        $token = $auth_header[sizeof($auth_header) - 1];
        $token_parts = explode('.', $token);
        $token_header = $token_parts[1];
        $token_header_json = base64_decode($token_header);
        $token_header_array = json_decode($token_header_json, true);
        $user_token = $token_header_array['jti'];
        $user = DB::table('oauth_access_tokens')->where('id', $user_token)->first();
        return $user;
    }

    public function broadcastNotifikasi(User $user, Model $model){
        if ($model instanceOf Komentar){
            $event = "{$model->jenis_induk}-komentar";

            $this->kirimNotifikasiViaGcm($event, [
                'id' => $model->id_induk,
                'pesan' => $model->user->nama.' mengomentari kiriman Anda.',
                'nama' => $model->user->nama
            ], $this->komentar->ambilTokenPemilik($model)->all());

            if (env('USE_ONESIGNAL', false)){
                $this->kirimNotifikasiViaOnesignal($event, [
                    'id' => $model->id_induk,
                    'pesan' => $model->user->nama . ' mengomentari kiriman Anda',
                    'nama' => $model->user->nama
                ], $this->komentar->ambilIdPemilikOneSignal($model)->all());


                $penerimaOneSignal = $this->komentar->ambilIdOneSignal($model)->all();

                if (count($penerimaOneSignal)){
                    $this->kirimNotifikasiViaOnesignal($event, [
                        'id' => $model->id_induk,
                        'pesan' => $model->user->nama.' juga mengomentari kiriman yang anda ikuti.',
                        'nama' => $model->user->nama
                    ], $penerimaOneSignal);
                }
            }

            $penerima = $this->komentar->ambilToken($model)->all();


            if (count($penerima)) {
                $this->kirimNotifikasiViaGcm($event, [
                    'id' => $model->id_induk,
                    'pesan' => $model->user->nama.' juga mengomentari kiriman yang Anda ikuti.',
                    'nama' => $model->user->nama
                ], $penerima);
            }
        } else if ($model instanceOf TindakLanjut) {
            $event = "kejadian-tindaklanjut";

            $data = [
                'id' => $model->id_kejadian,
                'pesan' => "Tindak lanjut baru",
                'status' => $model->status,
                'nama' => $model->user->nama
            ];
            
            $penerima = $this->tindaklanjut->ambilTokenPemilik($model);
            $penerima = $penerima->merge($this->tindaklanjut->ambilToken($model))->all();

            $this->kirimNotifikasiViaGcm($event, $data, $penerima);

            if (env('USE_ONESIGNAL', false)){
                $penerimaOneSignal = $this->tindaklanjut->ambilIdPemilik($model);
                $penerimaOneSignal = $penerimaOneSignal->merge($this->tindaklanjut->ambilId($model))->all();

                $this->kirimNotifikasiViaOnesignal($event, $data, $penerimaOneSignal);
            }
        } else {
            $jenisModel = $model instanceOf Kejadian ? 'kejadian' : ($model instanceOf Pengaduan ? 'pengaduan' : ($model instanceOf Kegiatan ? 'kegiatan' : 'tr'));

            $event = "{$jenisModel}-baru";

            $data = [
                'id' => $model->id,
                'nama' => $model->user->nama
            ];

            if ($jenisModel === 'pengaduan') {
                $data['pesan'] = 'Pengaduan baru';
            } else if ($jenisModel === 'kegiatan'){
                $data['pesan'] = 'Kegiatan baru';

                if ($user->jenis_pemilik === 'personil') {
                    $penerimaOneSignal = $this->personil->ambilIdLain($user->pemilik->id);
                    $penerimaOneSignal = $penerimaOneSignal->merge($this->bhabin->ambilId())->all();

                    $penerima = $this->personil->ambilTokenLain($user->pemilik->id);
                    $penerima = $penerima->merge($this->bhabin->ambilToken())->all();
                } else {
                    $penerimaOneSignal = $this->bhabin->ambilIdLain($user->pemilik->personil->id);
                    $penerimaOneSignal = $penerimaOneSignal->merge($this->personil->ambilId())->all();

                    $penerima = $this->bhabin->ambilTokenLain($user->pemilik->personil->id);
                    $penerima = $penerima->merge($this->personil->ambilToken())->all();
                }
            } else {
                $data = [
                    'id' => $model->id,
                    'pesan' => 'Kejadian baru',
                    'kejadian' => $model->kejadian,
                    'lokasi' => $model->lokasi,
                    'nama' => $model->user->nama,
                    'jabatan' => $model->user->jabatan,
                    'foto' => $model->user->foto,
                    'lat' => $model->lat,
                    'lng' => $model->lng
                ];

                if ($user->jenis_pemilik === 'personil' || $user->jenis_pemilik === 'bhabin'){
                    $penerimaOneSignal = $this->personil->ambilIdLain($user->pemilik->id);
                    $penerimaOneSignal = $penerimaOneSignal->merge($this->bhabin->ambilId())->all();

                    $penerima = $this->personil->ambilTokenLain($user->pemilik->id);
                    $penerima = $penerima->merge($this->bhabin->ambilToken())->all();
                } else if ($user->jenis_pemilik === 'instansi' || $user->jenis_pemilik === 'masyarakat'){
                    $penerimaOneSignal = $this->bhabin->ambilId();
                    $penerimaOneSignal = $penerimaOneSignal->merge($this->personil->ambilId());

                    $penerima = $this->bhabin->ambilToken();
                    $penerima = $penerima->merge($this->personil->ambilToken()); 
                } else { // Admin
                    $penerimaOneSignal = $this->bhabin->ambilId();
                    $penerimaOneSignal = $penerimaOneSignal->merge($this->personil->ambilId())->all();

                    $penerima = $this->personil->ambilToken();
                    $penerima = $penerima->merge($this->bhabin->ambilToken())->all();
                }
            }

            if (env('USE_ONESIGNAL', false))
                $this->kirimNotifikasiViaOnesignal($event, $data, $penerimaOneSignal);

            $this->kirimNotifikasiViaGcm($event, $data, $penerima);
        }
    }

    public function kirimNotifikasiViaOnesignal($event, $data, $penerima){
        \OneSignal::sendNotificationToExternalUser(
            $data['pesan'],
            $penerima,
            null,
            ['event' => $event, 'data' => $data]
        );
    }

    public function kirimNotifikasiViaGcm ($event, $data, $penerima){
        $optionBuilder = (new OptionsBuilder())->setTimeToLive(60*20);

        $notificationBuilder = (new PayloadNotificationBuilder())
                            ->setBody($data)
                            ->setTitle($event);
        $dataBuilder = (new PayloadDataBuilder())
                    ->addData(["event" => $event, 'data' => $data]);

        $option = $optionBuilder
                ->setPriority(OptionsPriorities::high)
                ->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        if(count($penerima)){
            $downstreamResponse = FCM::sendTo($penerima, $option, null, $data);

            $tokenToDelete = [];
            if ($downstreamResponse->numberFailure() > 0 || $downstreamResponse->numberModification() > 0) {
                $tokenToDelete = collect($downstreamResponse->tokensToDelete());
                $tokenToDelete = $tokenToDelete->merge(collect($downstreamResponse->tokensWithError())->map(function ($key, $value){ return $key; }));

                User::whereIn('fcm_id', $tokenToDelete)->update(['fcm_id' => null]);

                foreach ($downstreamResponse->tokensToModify() as $key => $value){
                    User::where('fcm_id', $key)->update(['fcm_id' => $value]);
                }
            }
            return compact('downstreamResponse', 'penerima', 'tokenToDelete');
        }

        return null;
    }

    public function sendSms($to, $message, $phone=null){
        $client = new GuzzleClient(['timeout' => 5.0]);

        $response = $client->request('POST', env('SMS_URL'), ['headers' => ['Authorization' => env('SMS_TOKEN'), 'Accept' => 'application/json'],'form_params' => ['to' => $to, 'message' => $message, 'phone' => $phone]]);

        return $response;
    }

    public function broadcastNotifikasiKejadianVerified($user, $kejadian, $jenis, $kesatuan, $personil)
    {
        $data = [
            'id' => $kejadian->id,
            'pesan' => 'Kejadian baru',
            'kejadian' => $kejadian->kejadian,
            'lokasi' => $kejadian->lokasi,
            'nama' => $kejadian->user->nama,
            'jabatan' => $kejadian->user->jabatan,
            'foto' => $kejadian->user->foto,
            'lat' => $kejadian->lat,
            'lng' => $kejadian->lng
        ];

        $event = "kejadian-baru";

        if($jenis == 'kesatuan') {
            $penerima = $this->personil->ambilTokenByKesatuan($kesatuan);
        } else {
            $penerima = $this->personil->ambilTokenById($personil);
        }

        $this->kirimNotifikasiViaGcm($event, $data, $penerima->all());
    }
}
