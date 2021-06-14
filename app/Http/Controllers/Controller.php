<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Kejadian;
use App\Models\Komentar;
use App\Models\Masyarakat;
use App\Models\Pengaduan;
use App\Models\Personil;
use App\Models\TindakLanjut;
use App\Models\User;
use App\Notifications\KegiatanNotification;
use App\Notifications\KejadianNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $personil, $masyarakat, $kegiatan, $komentar, $tindaklanjut;

    public function __construct()
    {
        $this->personil = new Personil();
        $this->masyarakat = new Masyarakat();
        $this->komentar = new Komentar();
        $this->kegiatan = new Kegiatan();
        $this->tindaklanjut = new TindakLanjut();
    }


    public static function userAccessTokenId($access_token){
        $user_token = (new Parser(new JoseEncoder()))->parse($access_token)->claims()->all()['jti'];
        return DB::table('oauth_access_tokens')->where('id', $user_token)->first();
    }


    public function broadcastNotifikasi(User $user, Model $model){
        if ($model instanceOf Komentar){
            $event = "{$model->jenis_induk}-komentar";
            $this->kirimNotifikasiViaGcm($event, [
                'id' => $model->id_induk,
                'pesan' => $model->user->nama.' mengomentari kiriman Anda.',
                'nama' => $model->user->nama
            ], $this->komentar->ambilTokenPemilik($model)->all());

            $notification = $model->jenis_induk == 'kegiatan' ? new KegiatanNotification($model->induk, $model->user->nama.' mengomentari kiriman Anda.') : new KejadianNotification($model->induk, $model->user->nama.' mengomentari kiriman Anda.');
            Notification::send($model->induk->user, $notification);

            if (env('USE_ONESIGNAL', false)){
                $penerimaOneSignal = $this->komentar->ambilIdPemilikOneSignal($model)->all();
                if (count($penerimaOneSignal)){
                    $this->kirimNotifikasiViaOnesignal($event, [
                        'id' => $model->id_induk,
                        'pesan' => $model->user->nama . ' mengomentari kiriman Anda',
                        'nama' => $model->user->nama
                    ], $this->komentar->ambilIdPemilikOneSignal($model)->all());
                }

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

                $penerimaUser =  $this->komentar->ambilUser($model);

                $notification = $model->jenis_induk == 'kegiatan' ? new KegiatanNotification($model->induk, $model->user->nama.' juga mengomentari kiriman yang Anda ikuti.') : new KejadianNotification($model->induk, $model->user->nama.' juga mengomentari kiriman yang Anda ikuti.');
                Notification::send($penerimaUser, $notification);
            }
        } else if ($model instanceOf TindakLanjut) {
            $event = "kejadian-tindaklanjut";
            $data = [
                'id' => $model->id_kejadian,
                'pesan' => "Tindak lanjut headline baru. ".$model->status,
                'status' => $model->status,
                'nama' => $model->user->nama
            ];

            $penerima = $this->tindaklanjut->ambilTokenPemilik($model);
            $penerima = $penerima->merge($this->tindaklanjut->ambilToken($model))->all();
            $this->kirimNotifikasiViaGcm($event, $data, $penerima);

            $penerimaOneSignal = $this->tindaklanjut->ambilIdPemilik($model);
            $penerimaOneSignal = $penerimaOneSignal->merge($this->tindaklanjut->ambilId($model))->all();
            if (env('USE_ONESIGNAL', false)){
                $this->kirimNotifikasiViaOnesignal($event, $data, $penerimaOneSignal);
            }

            $user = User::whereIn('id', $penerimaOneSignal)->get();
            Notification::send($user, new KejadianNotification($model->kejadian, $data['pesan']));
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
                $data['pesan'] = $model->is_quick_response == 1 ? $user->nama .' menambahkan Quick Response baru' : $user->nama .' menambahkan Kegiatan baru';
                $data['dokumentasi'] = $model->dokumentasi;
                if ($user->jenis_pemilik === 'personil') {
                    $penerimaOneSignal = $this->personil->ambilIdLain($user->pemilik->id)->all();
                    $penerima = $this->personil->ambilTokenLain($user->pemilik->id)->all();
                } else {
                    $penerimaOneSignal = $this->personil->ambilId()->all();
                    $penerima = $this->personil->ambilToken()->all();
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
                if ($user->jenis_pemilik === 'personil'){
                    $penerimaOneSignal = $this->personil->ambilIdLain($user->pemilik->id)->all();
                    $penerima = $this->personil->ambilTokenLain($user->pemilik->id)->all();
                } else if ($user->jenis_pemilik === 'masyarakat'){
                    $penerimaOneSignal = $this->personil->ambilId()->all();
                    $penerima = $this->personil->ambilToken()->all();
                } else { // Admin
                    $penerimaOneSignal = $this->personil->ambilId()->all();
                    $penerima = $this->personil->ambilToken()->all();
                }
            }
            if (env('USE_ONESIGNAL'))
                $this->kirimNotifikasiViaOnesignal($event, $data, $penerimaOneSignal);

            $notifikasi = $model instanceOf Kejadian ? new KejadianNotification($model, $data['pesan']) : new KegiatanNotification($model, $data['pesan']);
            $userPenerima = User::whereIn('id', $penerimaOneSignal)->get();
            Notification::send($userPenerima, $notifikasi);

            $this->kirimNotifikasiViaGcm($event, $data, $penerima);
        }
    }

    public function kirimNotifikasiViaOnesignal($event, $data, $penerima){
        $penerimaCollection = collect($penerima);
        $penerima = $penerimaCollection->map(function ($item){ return (string) $item;})->all();
        $parameters = [
            'headings'       => [
                'en' => $data['pesan'],
            ],
            'include_external_user_ids' => $penerima,
            'content_available' => true,
            'mutable_content' => true,
            'data' => ['event' => $event, 'data' => $data],
            "ios_category" => $event,
        ];

        switch ($event) {
            case 'kegiatan-baru':
                // $parameters['contents'] = [ 'en' => $data['nama'].' menambah '.$data['pesan']];
                $parameters['ios_attachments'] = [ "id" => url('/api/upload').'/'.$data['dokumentasi']];
                break;
            case 'kejadian-baru':
                $parameters['contents'] = [
                    'en' => $data['nama'].' menambah '.$data['pesan'].' - '.$data['kejadian']. ' | '.$data['lokasi']];
                $parameters['ios_sound'] = 'emergency.caf';
                break;
            case 'kejadian-luar-biasa-baru':
                // $parameters['headings'] = [ 'en' => 'PANGGILAN LUAR BIASA' ];
                $parameters['contents'] = [ 'en' => $data['nama'].' : '.$data['pesan'] ];
                $parameters['ios_sound'] = 'emergency.caf';
                break;
            case 'incoming-vcon':
                $parameters['ios_sound'] = 'vcall_ringtone.caf';
                break;
        }

        \OneSignal::sendNotificationCustom($parameters);
        
        // \OneSignal::sendNotificationToExternalUser(
        //     $data['pesan'],
        //     $penerima,
        //     null,
        //     ['event' => $event, 'data' => $data],
        // );
    }

    public function checkPlayerAndUpdateIfExists($id, $userId){
        $checkPlayer = \OneSignal::sendPlayer([], 'GET', \OneSignal::ENDPOINT_PLAYERS.'/'.$id);

        if ($checkPlayer){
            \OneSignal::editPlayer([
                'id' => $id,
                'external_user_id' => $userId
            ]);
        }
    }

    public function sendNotificationToExternalUser($message, $userId, $url = null, $data = null, $buttons = null, $schedule = null, $headings = null, $subtitle = null) {
        $contents = array(
            "en" => $message
        );

        $params = array(
            'app_id' => env('ONESIGNAL_ID'),
            'contents' => $contents,
            'include_external_user_ids' => is_array($userId) ? $userId : array($userId)
        );

        if (isset($url)) {
            $params['url'] = $url;
        }

        if (isset($data)) {
            $params['data'] = $data;
        }

        if (isset($buttons)) {
            $params['buttons'] = $buttons;
        }

        if(isset($schedule)){
            $params['send_after'] = $schedule;
        }

        if(isset($headings)){
            $params['headings'] = array(
                "en" => $headings
            );
        }

        if(isset($subtitle)){
            $params['subtitle'] = array(
                "en" => $subtitle
            );
        }

        $oSignal = \OneSignal::sendNotificationCustom($params);
        $oSignalResponse = $oSignal->getBody();
        return json_decode($oSignalResponse);
    }

    public function kirimNotifikasiViaGcm ($event, $data, $penerima){
        $optionBuilder = (new OptionsBuilder())->setTimeToLive(60*30);

        $notificationBuilder = (new PayloadNotificationBuilder("FCM"))
            ->setBody($data)
            ->setTitle($event);
        $dataBuilder = (new PayloadDataBuilder())
            ->addData(["event" => $event, 'data' => $data]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        if(count($penerima)){
            $downstreamResponse = FCM::sendTo($penerima, $option, null, $data);
            $tokenToDelete = null;
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
        $response = Http::timeout(5)->withToken(env('SMS_TOKEN'))->post(env('SMS_URL'), [
            'to' => $to,
            'message' => $message,
            'phone' => $phone
        ]);

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
            $penerimaOneSignal = $this->personil->ambilIdUserByKesatuan($kesatuan);
        } else if($jenis == 'terdekat'){
            $penerima = $this->personil->ambilTokenById($personil);
            $penerimaOneSignal = $this->personil->ambilIdUserById($personil);
        } else {
            $penerimaOneSignal = $this->personil->ambilId();
            $penerima = $this->personil->ambilToken();
        }

        if (env('USE_ONESIGNAL')){
            $this->kirimNotifikasiViaOnesignal($event, $data, $penerimaOneSignal->all());
        }

        $users = User::whereIn('id', $penerimaOneSignal->all());
        Notification::send($users, new KejadianNotification($kejadian, $data['pesan']));

        $this->kirimNotifikasiViaGcm($event, $data, $penerima->all());
    }
}
