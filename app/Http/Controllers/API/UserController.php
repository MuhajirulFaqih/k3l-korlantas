<?php

namespace App\Http\Controllers\API;

use App\Models\Darurat;
use App\Events\LacakMasyarakatEvent;
use App\Events\LacakPersonilEvent;
use App\Events\PersonilLogoutEvent;
use App\Http\Controllers\Controller;
use App\Models\Kejadian;
use App\Mail\ForgotPassword;
use App\Mail\WelcomeMessage;
use App\Models\Masyarakat;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\LogMasyarakatTransformer;
use App\Transformers\PersonilTransformer;
use App\Transformers\ResponseUserTransformer;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class UserController extends Controller
{

    public function loginSocialMedia(Request $request){
        try {
            $provider = $request->provider;
            $token = $provider == 'google' ? $this->getOauth2Token($request->accessToken) : $request->accessToken;
            //$token = $request->accessToken;
            //dd($token);

            $socialite = $provider == 'google' ? Socialite::driver($provider)->scopes(['profile','email'])->userFromToken($token) : Socialite::driver($provider)->userFromToken($token);

            if (!$exist = Masyarakat::where('provider', $provider)->where('provider_id', $socialite->getId())->first()){
                //Create user

                // Check if user's email is Avaliable in our db so set provider and provider id associate with this socialite
                if($user = User::where('username', $socialite->getEmail())->first()){
                    $pemilik = $user->pemilik;

                    $data = [
                        'provider' => $provider,
                        'provider_id' => $socialite->getId()
                    ];

                    // Check if foto null then grab from socialite
                    if (!$pemilik->foto){
                        $contents = file_get_contents($socialite->getAvatar());
                        $name = str_random(40).'.jpg';

                        Storage::put('masyarakat/'.$name, $contents);

                        $data['foto'] = 'masyarakat/'.$name;
                    }

                    $pemilik->update($data);
                } else {
                    $contents = file_get_contents($socialite->getAvatar());
                    $name = $name = str_random(40).'.jpg';

                    $foto = Storage::put('masyarakat/'.$name, $contents);

                    $masyarakat = Masyarakat::create([
                        'nama' => $socialite->getName(),
                        'provider' => $provider,
                        'provider_id' => $socialite->getId(),
                        'foto' => 'masyarakat/'.$name
                    ]);

                    $password = str_random(6);

                    $auth = $masyarakat->auth()->create([
                            'username' => $socialite->getEmail(),
                            'password' => bcrypt($password)
                        ]);

                    if ($masyarakat && $auth)
                        Mail::to($auth->username)
                            ->send(new WelcomeMessage($auth, $password));

                }
            }
            return response()->json($this->issueToken($request, $provider, $token));
        } catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getOauth2Token($code){
        $client = new Client();

        $responseRequest = $client->post("https://accounts.google.com/o/oauth2/token", [
            "form_params" => [
                'grant_type' => 'authorization_code',
                'client_id' => env('GOOGLE_CLIENT_ID'),
                'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                'code' => $code
            ]
        ]);

        $body = json_decode($responseRequest->getBody());

        Log::info('Response oauth2token', (array) $body);

        if (isset($body->access_token))
            return $body->access_token;

        return null;
    }

    private function getTokenFromGoogle($token){
        $client = new \Google_Client([
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET')
        ]);

        $data = $client->fetchAccessTokenWithAuthCode($token);

        return $data;

        //return $client->getAccessToken();;
    }


    private function issueToken($request, $provider, $accessToken){
        $params = [
            'grant_type' => 'social',
            'client_id' => (int) env('MIX_CLIENT_ID'),
            'client_secret' => env('MIX_CLIENT_SECRET'),
            'accessToken' => $accessToken,
            'privider' => $provider
        ];

        $request->request->add($params);

        $requestToken = Request::create("api/user/auth", "POST");
        $response = \Route::dispatch($requestToken);

        return json_decode((string) $response->getContent(), true);
    }

    public function ubahNomor(Request $request){
        $user = $request->user();

        if (!$user->pemilik instanceof Masyarakat)
            return response()->json(['error' => 'Tidak memiliki akses'], 403);

        $user->diverifikasi = false;
        do{
            $user->kode = rand(1000, 10000);
        }while(User::where('kode', $user->kode)->where('diverifikasi', 0)->count() > 0);

        $masyarakat = $user->pemilik;

        $masyarakat->no_telp = $request->telp;

        $masyarakat->save();
        $user->save();

        $this->sendSms($request->telp, env('APP_MASYARAKAT_NAME')." - Kode otentikasi: {$user->kode}. Demi keamanan, jangan berikan kode RAHASIA ini kepada siapapun.");

        return response()->json(['success' => true]);
    }

    public function ubahNomorO(Request $request){
        $user = $request->user();

        if (!$user->pemilik instanceof Masyarakat)
            return response()->json(['error' => 'Tidak memiliki akses'], 403);

        $user->diverifikasi = false;
        do{
            $user->kode = rand(1000, 10000);
        }while(User::where('kode', $user->kode)->where('diverifikasi', 0)->count() > 0);

        $masyarakat = $user->pemilik;

        $masyarakat->no_telp = $request->telp;

        $masyarakat->save();
        $user->save();

        $this->sendSms($request->telp, "<#> ".env('APP_MASYARAKAT_NAME')." - Kode otentikasi: {$user->kode}. Demi keamanan, jangan berikan kode RAHASIA ini kepada siapapun.".$request->hash);

        return response()->json(['success' => true]);
    }

    public function smsOtpO(Request $request){
        $user = $request->user();

        do {
            $kode = rand(1000, 10000);
        } while(User::where('kode', $kode)->where('diverifikasi', 0)->count()> 0);

        if(!$user->update(['kode' => $kode]))
            return response()->json(['error' => 'Terjadi Kesalahan'], 500);

        $this->sendSms($user->pemilik->no_telp, "<#>".env('APP_MASYARAKAT_NAME')." - Kode otentikasi: {$kode}. Demi keamanan, jangan berikan kode ini kepada siapapun.\n".$request->hash);

        return response()->json(['success' => true]);
    }

    public function registerO(Request $request){
        $validData = $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:user,username',
            'no_telp' => 'required|min:8|max:13|unique:masyarakat',
            'alamat' => 'required|min:5',
            'hash' => 'required',
            'nik' => 'nullable',
            //'id_kel' => 'required|min:10',
            'password' => 'required',
        ]);

        $masyarakat = Masyarakat::create([
            'nama' => $validData['nama'],
            'alamat' => $validData['alamat'],
            'no_telp' => $validData['no_telp'],
            'nik' => $validData['nik']
            //'id_kel' => $validData['id_kel']
        ]);

        do {
            $kode = rand(1000, 10000);
        } while (User::where('kode', $kode)->where('diverifikasi', 0)->count() > 0);

        $user = $masyarakat->auth()->create([
            'password' => bcrypt($validData['password']),
            'username' => $validData['email'],
            'kode' => $kode
        ]);

        if (!$masyarakat && !$user)
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        // Todo Send sms
        $this->sendSms($masyarakat->no_telp, "<#> ".env('APP_MASYARAKAT_NAME')." - Kode otentikasi: {$kode}. Demi keamanan, jangan berikan kode RAHASIA ini kepada siapapun.\n".$validData['hash']);


        // Send email
        if (env('MAIL_USERNAME'))
            Mail::to($validData['email']) ->send(new WelcomeMessage($user));

        return response()->json(['success' => true, 'id' => $user->id ], 201);
    }

    public function ubahNik(Request $request){
        $user = $request->user();

        if ($user->jenis_pemilik != 'masyarakat')
            return response()->json(['error' => 'Terlarang'], 403);

        $masyarakat = $user->pemilik;

        $masyarakat->nik = $request->nik;

        if(!$masyarakat->save())
            return response()->json(['error' => 'Terjadi kesalahan'], 500);


        return response()->json(['success' => true]);
    }

    public function register(Request $request)
    {
        $validData = $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:user,username',
            'no_telp' => 'required|min:8|max:13|unique:masyarakat',
            'alamat' => 'required|min:5',
            //'id_kel' => 'required|min:10',
            'password' => 'required',
        ]);
        
        $masyarakat = Masyarakat::create([
            'nama' => $validData['nama'],
            'alamat' => $validData['alamat'],
            'no_telp' => $validData['no_telp'],
            //'id_kel' => $validData['id_kel']
        ]);

        do {
            $kode = rand(1000, 10000);
        } while (User::where('kode', $kode)->where('diverifikasi', 0)->count() > 0);

        $user = $masyarakat->auth()->create([
            'password' => bcrypt($validData['password']),
            'username' => $validData['email'],
            'kode' => $kode
        ]);

        if (!$masyarakat && !$user)
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        // Todo Send sms
        $this->sendSms($masyarakat->no_telp, env('APP_MASYARAKAT_NAME')." - Kode otentikasi: {$kode}. Demi keamanan, jangan berikan kode RAHASIA ini kepada siapapun.");


        // Send email
        Mail::to($validData['email'])
            ->send(new WelcomeMessage($user));

        return response()->json(['success' => true, 'id' => $user->id ], 201);
    }

    public function kode_verifikasi(Request $request){
        $otp = $request->otp;
        $idUser = $request->idUser;

        $user = User::find($idUser);

        if (!$user)
            return response()->json(['error' => 'Pengguna tidak ditemukan'], 403);

        if ($otp != $user->kode){
            return response()->json(['error' => 'Kode Verifikasi tidak sesuai'], 500);
        }

        $user->diverifikasi = true;
        $user->save();
        return response()->json(['success' => true, 'id' => $user->id]);
    }

    public function details(Request $request)
    {
        $user = $request->user();

        return fractal()
                ->item($user)
                ->parseIncludes('pemilik.personil')
                ->transformWith(ResponseUserTransformer::class)
                ->serializeWith(DataArraySansIncludeSerializer::class)
                ->respond();
    }
    
    public function change_password(Request $request)
    {
        $user = $request->user();

        $validData = $request->validate([
            'old_password' => 'required|min:6',
            'password' => 'required|min:6|confirmed'
        ]);

        if (!password_verify($validData['old_password'], $user->password)) 
            return response()->json(['errors' => ['Password lama tidak sesuai']], 422);

        $user->password = bcrypt($request->password);

        if(!$user->save())
            return response()->json(['error' => 'Terjadi kesalahan'], 500);
        
        return response()->json(['success' => true], 200);
    }

    public function change_password_admin(Request $request)
    {
        $user = $request->user();

        $validData = $request->validate([
            'username' => 'required',
            'password_lama' => 'required',
            'password_baru' => 'min:6|required_with:konfirmasi_password_baru|same:konfirmasi_password_baru',
            'konfirmasi_password_baru' => 'min:6'
        ]);

        if (!password_verify($validData['password_lama'], $user->password)) 
            return response()->json(['errors' => ['Password lama tidak sesuai']], 422);

        $user->username = $request->username;
        $user->password = bcrypt($request->password_baru);

        if(!$user->save())
            return response()->json(['error' => 'Terjadi kesalahan'], 500);
        
        return response()->json(['success' => true, 'username' => $user->username], 200);
    }

    public function logout(Request $request){
        $user = $request->user();
        $user->fcm_id = null;

        DB::table('oauth_access_tokens')->where('user_id', $user->id)->update(['revoked' => 1]);

        if(!$user->save())
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        $personil = $user->jenis_pemilik == 'personil' ? $user->pemilik : $user->pemilik->personil;

        $data = fractal()
            ->item($personil)
            ->transformWith(PersonilTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->toArray();

        event(new PersonilLogoutEvent($data));

        return response()->json(['success' => true], 200);
    }

    public function logoutAdmin(Request $request) {
        $user = $request->user();
        $user->fcm_id = null;

        DB::table('oauth_access_tokens')->where('user_id', $user->id)->update(['revoked' => 1]);

        if(!$user->save())
            return response()->json(['errors' => 'Terjadi kesalahan'], 500);

        return response()->json(['success' => true], 200);
    }

    public function resetPassword(Request $request){
        if (filter_var($request->username, FILTER_VALIDATE_EMAIL)){
            $user = User::where('username', $request->username)->first();
            if (!$user)
                return response()->json(['error' => 'Email tidak terdaftar'], 404);

            $password = str_random();
            $user->password = bcrypt($password);

            if(!$user->save())
                return response()->json(['error' => 'Terjadi kesalahan'], 500);

            Mail::to($user->username)
                ->send(new ForgotPassword($user, $password));

            return response()->json(['success' => true, 'message' => 'Email terkirim. Silahkan cek kotak masuk atau folder spam']);

        } elseif (preg_match("/\d{10,12}/", $request->username) === 1){
            $masyarakat = Masyarakat::where('no_telp', $request->username)->first();

            if (!$masyarakat)
                return response()->json(['error' => 'Nomor Handphone tidak terdaftar'], 404);

            $password = str_random();
            $user = $masyarakat->auth;
            $user->password = bcrypt($password);

            if (!$user->save())
                return response()->json(['error' => 'Terjadi kesalahan'], 500);

            $this->sendSms($request->username, env('APP_MASYARAKAT_NAME')." - Reset password. Login dengan akun berikut Email: {$user->username}, password: {$password}");

            return response()->json(['success' => true, 'message' => 'Sms terkirim']);
        }

        return response()->json(['error' => 'email atau no handphone tidak sesuai'], 422);
    }

    public function change_profil(Request $request)
    {
        $user = $request->user();

        $update = null;

        if ($user->jenis_pemilik == 'personil' || $user->jenis_pemilik == 'bhabin') {
            
            $validatedData = $request->validate([
                'no_hp'  => 'required',
                'alamat' => 'required',
            ]);

            $personil = $user->jenis_pemilik == 'personil' ? $user->pemilik : $user->pemilik->personil;
                
            $update = $personil->update([
                                    'no_telp'  => $validatedData['no_hp'],
                                    'alamat' => $validatedData['alamat'],
                                ]);

        } else if ($user->jenis_pemilik == 'masyarakat') {
            $validatedData = $request->validate([
                //'foto'      => 'required|image',
                'nama'      => 'required',
                'alamat'    => 'required',
                'no_telp'   => 'required'
            ]);
            /*$foto = $request->file('foto')
                            ->storeAs('masyarakat',str_random(10).'.'.$request->file('foto')
                            ->extension());*/
            $update = $user->pemilik->update([
                                      'nama'    => $validatedData['nama'],
                                      'alamat'  => $validatedData['alamat'],
                                      'no_telp' => $validatedData['no_telp'],
                                      //'foto'    => $foto
                                  ]);
        }

        if (!$update)
            return response()->json(['error' => 'Terjadi Kesalahan', 500]);

        return response()->json(['success' => true], 200);
    }

    public function fcm(Request $request){
        $user = $request->user();

        $validData =  $request->validate([
            'fcm_id' => 'required|min:6'
        ]);
        $user->fcm_id = $validData['fcm_id'];
        if(!$user->save())
            return response()->json(['error' => "Terjadi kesalahan"], 500);

        return response()->json(['succes' => true]);
    }

    public function updatePP(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['bhabin', 'personil', 'masyarakat']))
            return response()->json(['error' => 'Terlarang'], 403);
        
        $validData = $request->validate([
            'foto' => 'required|image'
        ]);

        if ($user->jenis_pemilik == 'masyarakat'){
            $foto = $validData['foto']->storeAs(
                'masyarakat', str_random(40).'.'.$validData['foto']->extension()
            );

            $masyarakat = $user->pemilik;

            $update = $masyarakat->update([
               'foto' => $foto
            ]);

            if (!$update)
                return response()->json(['error' => 'Terjadi kesalahan'], 500);

        } else {
            $personil = $user->jenis_pemilik === 'personil' ? $user->pemilik : $user->pemilik->personil;

            $foto = $validData['foto']->storeAs(
                'personil', $personil->nrp.'.jpg'
            );
        }

        return response()->json(['success' => true]);
    }

    public function trackingMasyarakat(Request $request){
        $user = $request->user();

        if ($user->jenis_pemilik != 'masyarakat')
            return response()->json(['error' => 'Terlarang'], 403);

        $validatedData = $request->validate([
           'lat' => 'required',
           'lng' => 'required',
           'id_induk' => 'required',
           'jenis_induk' => 'required'
        ]);

        $induk = $validatedData['jenis_induk'] == 'kejadian' ? Kejadian::find($validatedData['id_induk']) : Darurat::find($validatedData['id_induk']);

        if (!$induk)
            return response()->json(['error' => 'Tidak ditemukan'], 404);

        if ($induk->selesai)
            return response()->json(['error' => 'Telah selesai'], 404);

        $logMasyarakat = $induk->logmasyarakat()->create(['lat' => (double) $validatedData['lat'], 'lng' => (double) $validatedData['lng'], 'id_user' => $user->id]);

        $data = fractal()
            ->item($logMasyarakat)
            ->transformWith(LogMasyarakatTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->toArray();

        // Todo Kirim monit
        event(new LacakMasyarakatEvent($data));

        return response()->json(['success' => 'true']);

    }

    public function tracking(Request $request)
    {
        $user = $request->user();
        
        if(!in_array($user->jenis_pemilik, ['personil', 'bhabin']))
            return response()->json(['error' => 'Terlarang'], 403);

        $validatedData = $request->validate([
            'lat' => 'required',
            'lng' => 'required',
            'angle' => 'required'
        ]);


        $personil = $user->jenis_pemilik == 'personil' ? $user->pemilik : $user->pemilik->personil;

        Log::info('Relokasi', ['lat' => $validatedData['lat'], 'lng' => $validatedData['lng'], 'personil' => $personil]);

        $update = [ 
              'lat' => (double) $validatedData['lat'],
              'lng' => (double) $validatedData['lng']
        ];

        $personil->lat = (double) $validatedData['lat'];
        $personil->lng = (double) $validatedData['lng'];
        $personil->updated_at = Carbon::now();


        if(!$personil->save())
            return response()->json(['error' => 'Terjadi kesalahan'], 500);


        // Tambah log personil
        if (in_array($personil->dinas->kegiatan, ['Patroli', 'Pengawalan'])){
            $log = $personil->logStatus->sortByDesc('created_at')->first();

            if ($log)
                $log->logpatroli()->create($update);
        }

        $data = fractal()
            ->item($personil)
            ->transformWith(PersonilTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->toArray();

        $data['data']['angle'] = $validatedData['angle'];

        event(new LacakPersonilEvent($data));

        return response()->json(['success' => true]);
    }

}