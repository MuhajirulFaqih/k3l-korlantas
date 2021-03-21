<?php

namespace App\Http\Controllers\API;
use App\Models\Darurat;
use App\Events\DaruratSelesaiEvent;
use App\Events\KejadianEvent;
use App\Events\TindakLanjutEvent;
use App\Http\Controllers\Controller;
use App\Models\Kejadian;
use App\Models\Komentar;
use App\Notifications\KejadianCreated;
use App\Models\Pengaturan;
use App\Models\Personil;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Models\TindakLanjut;
use App\Transformers\KejadianTransformer;
use App\Transformers\KomentarTransformer;
use App\Transformers\TindakLanjutTransformer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class KejadianController extends Controller
{
    public function create_kejadian(Request $request) {
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin', 'admin', 'masyarakat']))
            return response()->json(['error' => 'Anda tidak memiliki aksess ke halaman ini'], 403);

        $request->validate([
            'lokasi'        => 'required',
            'kejadian'      => 'required',
            'keterangan'    => 'required',
            'lat'           => 'required',
            'lng'           => 'required',
        ]);

        $store = [
                    'id_user'    => $request->id_asal != '' ? $request->id_asal : $user->id,
                    'w_kejadian' => Carbon::now(),
                    'kejadian'   => $request->kejadian,
                    'lokasi'     => $request->lokasi,
                    'keterangan' => $request->keterangan,
                    'lat'        => $request->lat,
                    'lng'        => $request->lng,
                    'id_darurat' => $request->id_darurat ?? null,
                    'verifikasi' => $request->id_darurat != '' ? 1 : null,
                    'follow_me'  => $request->follow_me == 'true' ? true : false
                ];
        
        $kejadian = Kejadian::create($store);

        if (!$kejadian)
            return response()->json(['error' => 'terjadi kesalahan saat menyimpan data'], 500);

        // Todo send notification
        //Notification::send($user, new KejadianCreated($kejadian));
        //Jika asal dari darurat
        if($request->id_asal != '')
            $user = User::find($request->id_asal);

        //Jika kejadian dari masyarakat dan asal bukan dari admin
        if($user->jenis_pemilik == 'masyarakat' && $request->id_asal == '') {
            //Cek auto send notification
            /*$auto = Pengaturan::GetByKey('auto_send_notification')->first()->pluck('nilai');
            //Jika auto send notification aktif
            if($auto == "1") {
                $kejadian->update([
                    'verifikasi' => 1
                ]);
                $this->broadcastNotifikasi($user, $kejadian);
            }*/
        } else {
            if($request->id_asal == '') {
                // $this->broadcastNotifikasi($user, $kejadian); 
            }
        }

        $personilTerdekat = (new Personil())->terdekat($request->lat, $request->lng);

        foreach ($personilTerdekat as $row){
            $kejadian->nearby()->create([
                'id_personil' => $row['id'],
                'lat' => $row['lat'],
                'lng' => $row['lng']
            ]);
        }

        if($request->id_asal == '') {
            $data = fractal()
                    ->item($kejadian)
                    ->transformWith(new KejadianTransformer)
                    ->serializeWith(new DataArraySansIncludeSerializer)
                    ->toArray();

            event(new KejadianEvent($data));
        }

        return response()->json(['success' => true, 'id' => $kejadian->id], 201);
    }

    public function unfollow(Request $request, Kejadian $kejadian){
        $user = $request->user();

        if ($user->jenis_pemilik != 'masyarakat' && $kejadian->id_user != $user->id)
            return response()->json(['error' => 'Anda tidak memiliki akses'], 403);


        if(!$kejadian->update(['follow_me' => false]))
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        return response()->json(['success' => true]);
    }

    public function buatTindakLanjut(Request $request, Kejadian $kejadian){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin', 'admin']))
            return response()->json(['error' => 'Anda tidak memiliki akses'], 403);


        $validatedData = $request->validate([
            'status' => 'required|in:proses_penanganan,selesai',
            'keterangan' => 'required|min:8',
            'foto' => 'required|image'
        ]);

        $foto = $validatedData['foto']->storeAs('Tindaklanjut',str_random(40). '.' . $request->file('foto')->extension());

        $tindakLanjut = $kejadian->tindak_lanjut()->create([
            'keterangan'  => $validatedData['keterangan'],
            'id_user'     => $user->id,
            'foto'        => $foto,
            'status'      => $validatedData['status'],
        ]);

        if (!$tindakLanjut)
            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan data'], 500);

        if ($tindakLanjut->status == 'selesai') {

            $kejadian = $tindakLanjut->kejadian;
            $kejadian->update(['selesai' => true, 'follow_me' => false]);

            //Jika kejadian selesai, otomatis darurat selesai
            if(!is_null($kejadian->id_darurat)) {
                $darurat = Darurat::find($kejadian->id_darurat);
                if(!$darurat->update(['selesai' => true]))
                    return response()->json(['error' => 'Terjadi kesalahan'], 500);
                
                if(!env('APP_DEV')) {
                    $this->kirimNotifikasiViaGcm('darurat-selesai', $darurat->toArray(), [$darurat->user->fcm_id]);
                }

                broadcast(new DaruratSelesaiEvent($darurat));

                if (env('USE_ONESIGNAL', false)) {
                    if(!env('APP_DEV')) {
                        $this->kirimNotifikasiViaOnesignal('darurat-selesai', $darurat->toArray(), [$darurat->user->id]);
                    }
                }
            }
        }

        // Send notif
        if(!env('APP_DEV')) {
            $this->broadcastNotifikasi($user, $tindakLanjut);
        }

        $fractal = fractal()
            ->item($tindakLanjut)
            ->transformWith(TindakLanjutTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class);
        // Broadcast to monit
        $data = $fractal->toArray();
        event(new TindakLanjutEvent($data['data']));

        return response()->json($fractal->respond(), 201);
    }

    public function listkejadian(Request $request)
    {
        $user = $request->user();

        if(!in_array($user->jenis_pemilik, ['personil', 'bhabin', 'admin', 'masyarakat']))
            return response()->json(['error' => 'Anda tidak memiliki akses ke halaman ini'], 403);

        list($orderBy, $direction) = explode(':', $request->sort);

        if($user->jenis_pemilik == 'admin') {
            $kejadian = Kejadian::filtered($request->filter, $request->status)
                                ->orderBy($orderBy, $direction);
        } else {
            $kejadian = $request->filter == '' ?
                    Kejadian::filteredUser($user)->orderBy($orderBy, $direction):
                    Kejadian::filteredUser($user)->orderBy($orderBy, $direction);
        }

        
        if (count($kejadian->paginate(1)) === 0)
            return response()->json(['message' => 'Tidak ada content'], 204);
        
        $limit = $request->limit != '' ? $request->limit : 10;
        if($limit == 0)
            return null;
        $paginator = $kejadian->paginate($limit);
        $collection = $paginator->getCollection();


        return fractal()
                ->collection($collection)
                ->transformWith(new KejadianTransformer(true))
                ->serializeWith(new DataArraySansIncludeSerializer)
                ->paginateWith(new IlluminatePaginatorAdapter($paginator))
                ->respond();
    }

    public function getDetail(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin']))
            return response()->json(['error' => 'Ada tidak memiliki akses ke halaman ini'], 403);

        $semua = Kejadian::get();
        $bln  = Kejadian::bulan()->get();
        $output = [
            'semua' => [
                'total' => $semua->count(),
                'ditangani' => $semua->filter(function ($value) { return optional($value->tindak_lanjut->sortByDesc('created_at')->first())->status == 'proses_penanganan'; })->count(),
                'selesai' => $semua->filter(function ($value) { return optional($value->tindak_lanjut->sortByDesc('created_at')->first())->status == 'selesai'; })->count()
            ],
            'bln' => [
                'total' => $bln->count(),
                'ditangani' => $bln->filter(function ($value) { return optional($value->tindak_lanjut->sortByDesc('created_at')->first())->status == 'proses_penanganan'; })->count(),
                'selesai' => $bln->filter(function ($value) { return optional($value->tindak_lanjut->sortByDesc('created_at')->first())->status == 'selesai'; })->count()
            ]
        ];

        return response()->json($output);
    }

    public function detailkejadian(Request $request, Kejadian $kejadian)
    {
        $user = $request->user();

        if(!in_array($user->jenis_pemilik, ['admin', 'personil', 'bhabin', 'masyarakat']))
            return response()->json(['error' => 'Terlarang'], 403);

        if($user->jenis_pemilik == 'masyarakat' && $user->id != $kejadian->id_user)
            return response()->json(['error' => 'Terlarang'], 403);

        return fractal()
            ->item($kejadian)
            ->parseIncludes(['tindak_lanjut'])
            ->transformWith(new KejadianTransformer)
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->respond();
    }

    public function postkomentar(Kejadian $kejadian, Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'komentar' => 'required',
        ]);

        $komentar = $kejadian->komentar()->create([
            'komentar' => $validatedData['komentar'],
            'id_user'  => $user->id,
        ]);

        if (!$komentar)
            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan data'], 500);

        // Todo Notifikasi

        return response()->json(['success' => true], 201);
    }

    public function broadcast(Request $request)
    {
        $id = $request->id;
        $kejadian = Kejadian::find($id);
        $user = User::find($kejadian->id_user);
        
        $kejadian->update([
            'verifikasi' => 1
        ]);

        if($request->jenis == 'semua') { 
            if(!env('APP_DEV')) {
                $this->broadcastNotifikasi($user, $kejadian); 
            }
        } else {
            if(!env('APP_DEV')) {
                $this->broadcastNotifikasiKejadianVerified($request->user(), 
                                                        $kejadian, 
                                                        $request->jenis, 
                                                        $request->kesatuan,
                                                        $request->personil);
            }
        }

        return response()->json(['success' => true], 200);
    }

    public function total(Request $request)
    {
        if(!in_array($request->user()->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Terlarang'], 403);
        $data['total'] = Kejadian::count();
        $data['proses'] = Kejadian::filterProsesPenanganan()->count();
        $data['selesai'] = Kejadian::filterSelesai()->count();
        $data['belum'] = Kejadian::filterBelumDitangani()->count();

        return response()->json([$data]);
    }
}
