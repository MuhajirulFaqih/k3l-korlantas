<?php

namespace App\Http\Controllers\API;

use App\Models\Absensi;
use App\Events\LacakPersonilEvent;
use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\LogPersonil;
use App\Models\Pengaturan;
use App\Models\Personil;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\PersonilTransformer;
use App\Transformers\LogPersonilTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Spatie\Fractalistic\ArraySerializer;

class PersonilController extends Controller
{
    public function index(Request $request, Jabatan $jabatan)
    {
        $user = $request->user();
        list($orderBy, $direction) = explode(':', $request->sort);

        if (!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Anda tidak memiliki aksess di halaman ini'], 403);

        $paginator = Personil::search($request->filter)
            ->orderBy($orderBy, $direction)
            ->paginate(10);

        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->parseIncludes('bhabin')
            ->transformWith(new PersonilTransformer())
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function uploadFoto(Request $request)
    {
        $user = $request->user();

        if ($user->jenis_pemilik !== 'admin')
            return response()->json(['error' => 'Terlarang'], 403);

        $validData = $request->validate([
            'foto' => 'required|image'
        ]);

        /* $personil = Personil::find($validData['id_personil']);

        if (!$personil)
            return response()->json(['error', 'Personil tidak ditemukan'], 404); */

        $foto = $validData['foto']->storeAs(
            'personil',
            str_random(40) . '.jpg'
        );

        if (!$foto)
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        return response()->json(['success' => true, 'foto' => $foto]);
    }

    public function ubahPttHt(Request $request, Personil $personil){
        $user = $request->user();

        if ($user->jenis_pemilik !== 'admin')
            return response()->json(['error' => 'Terlarang'], 403);

        $personil->ptt_ht = !$personil->ptt_ht;

        if (!$personil->save())
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        return response()->json(['success' => true]);
    }

    public function tambah(Request $request)
    {
        $user = $request->user();

        if ($user->jenis_pemilik !== 'admin')
            return response()->json(['error' => 'Terlarang'], 403);

        $validData = $request->validate([
            'nama' => 'required|min:3',
            'nrp' => 'required|min:8|max:18|unique:personil',
            'alamat' => 'required|min:6',
            'id_jabatan' => 'required|numeric',
            'id_pangkat' => 'required|numeric',
            'id_kesatuan' => 'required|numeric',
            'foto' => 'nullable',
            'isBhabin' => 'required|boolean',
            'id_kelurahan' => 'array'
        ]);

        $personil = Personil::create($validData);

        if (!$personil)
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        if ($validData['foto'])
            \Storage::move($validData['foto'], 'personil/' . $personil->nrp . '.jpg');

        $pengaturan = Pengaturan::getByKey('default_password')->first();
        $password = bcrypt($pengaturan->nilai);

        if ($validData['isBhabin']) {
            if (!count($validData['id_kelurahan']))
                return response(['errors' => ['id_kelurahan' => 'Kelurahan tidak boleh kosong']], 422);

            $bhabin = $personil->bhabin()->create();
            $bhabin->kelurahan()->attach($validData['id_kelurahan']);

            $bhabin->auth()->create(['username' => $personil->nrp, 'password' => $password]);
        } else {
            $personil->auth()->create(['username' => $personil->nrp, 'password' => $password]);
        }

        return response()->json(['success' => true], 201);
    }

    public function lihat(Request $request, $id)
    {
        $personil = Personil::withTrashed()->find($id);
        $user = $request->user();

        if ($user->jenis_pemilik !== 'admin')
            return response()->json(['error' => 'Terlarang'], 403);

        return fractal()
            ->item($personil)
            ->transformWith(PersonilTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }

    public function details(Request $request)
    {
        $user = $request->user();
        $output = fractal()
            ->item($user->pemilik)
            ->transformWith(new PersonilTransformer())
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->toArray();
        return response()->json($output);
    }

    public function edit(Request $request, Personil $personil)
    {
        $user = $request->user();

        if ($user->jenis_pemilik !== 'admin')
            return response()->json(['error' => 'Terlarang !!!'], 403);

        if (!$personil)
            return response()->json(['error' => 'Personil tidak ditemukan'], 404);

        $validData = $request->validate([
            'nama' => 'required|min:3',
            'nrp' => 'required|min:8|max:18',
            'alamat' => 'required|min:6',
            'id_jabatan' => 'required|numeric',
            'id_pangkat' => 'required|numeric',
            'id_kesatuan' => 'required|numeric',
            'foto' => 'nullable',
            'isBhabin' => 'required|boolean'
        ]);

        $user = $personil->bhabin ? $personil->bhabin->auth : $personil->auth;

        // Jika personil merupakan bhabin
        if ($personil->bhabin) {
            $bhabin = $personil->bhabin;
            // Check apakah akan di update menjadi bukan bhabin
            if (!$validData['isBhabin']) {
                // Bersihkan data kelurahan dan hapus data bhabin
                $bhabin->kelurahan()->detach();
                $bhabin->forceDelete();

                // Dapatkan user dari bhabin dan pindah pemilik dari bhabin ke personil
                $user->pemilik()->associate($personil)->save();
            } else {
                if (!count($request->id_kelurahan))
                    return response()->json(['errors' => ['id_kelurahan' => 'Bhabin harus minimal memiliki 1 kelurahan']], 422);
                $bhabin->kelurahan()->detach();
                foreach ($request->id_kelurahan as $id_kelurahan)
                    $bhabin->kelurahan()->attach($id_kelurahan);
            }
        } else if (!$personil->bhabin && $validData['isBhabin']) { // Jika personil bukan bhabin dan akan menjadi bhabin
            if (!count($request->id_kelurahan))
                return response()->json(['errors' => ['id_kelurahan' => 'Bhabin harus minimal memiliki 1 kelurahan']], 422);

            $bhabin = $personil->bhabin()->create();
            foreach ($request->id_kelurahan as $id_kelurahan)
                $bhabin->kelurahan()->attach($id_kelurahan);
            $user->pemilik()->associate($bhabin)->save();
        }

        $personil = Personil::find($personil->id);

        $user = $personil->bhabin ? $personil->bhabin->auth : $personil->auth;

        if ($personil->nrp != $request->nrp)
            $user->update(['username' => $request->nrp]);

        // Update data personil
        $update = $personil->update([
            'nama' => $validData['nama'],
            'nrp' => $validData['nrp'],
            'alamat' => $validData['alamat'],
            'id_jabatan' => $validData['id_jabatan'],
            'id_pangkat' => $validData['id_pangkat'],
            'id_kesatuan' => $validData['id_kesatuan']
        ]);

        if (!$update)
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        //dd($personil);

        // Update foto
        if (isset($validData['foto']) && $validData['foto']) {
            if (\Storage::exists('personil/' . $personil->nrp . '.jpg'))
                \Storage::delete('personil/' . $personil->nrp . '.jpg');
            \Storage::move($validData['foto'], 'personil/' . $personil->nrp . '.jpg');
        }

        return response()->json(['success' => true]);

    }

    public function updateStatusDinas(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin']))
            return response()->json(['error' => 'Anda tidak memiliki aksess di halaman ini'], 403);

        $validatedData = $request->validate([
            'id_dinas' => 'required',
            'lat' => 'required',
            'lng' => 'required'
        ]);

        $personil = $user->jenis_pemilik == 'personil' ? $user->pemilik : $user->pemilik->personil;
        $log = LogPersonil::where('id_personil', $personil->id)->latest()->first();
        $waktu = Carbon::now();
        if($log) {
            $log->update(['waktu_selesai_dinas' => $waktu]);
        }

        $update = $personil->update([
            'lat' => $validatedData['lat'],
            'lng' => $validatedData['lng'],
            'status_dinas' => $validatedData['id_dinas'],
            'updated_at' => Carbon::now(),
            'w_status_dinas' => Carbon::now(),
        ]);

        LogPersonil::create([
            'id_personil' => $personil->id,
            'status_dinas' => $validatedData['id_dinas'],
            'waktu_mulai_dinas' => $waktu
        ]);

        $this->absenPersonil($personil, $validatedData);

        if (!$update)
            return response()->json(['error' => 'Terjadi kesalahan saat menambah menyimpan data'], 500);

        $personil = Personil::find($personil->id);
        $data = fractal()
            ->item($personil)
            ->transformWith(PersonilTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->toArray();
        $data['data']['angle'] = 0;

        // Todo send monit
        event(new LacakPersonilEvent($data));

        return response()->json(['success' => true]);
    }

    public function tracking(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Anda tidak memiliki aksess di halaman ini'], 403);

        $paginator = Personil::terlacak('day', 1, $request->jenis)->orderBy('updated_at', 'desc')->paginate(10);
        $collection = $paginator->getCollection();

        $respond = fractal()
            ->collection($collection)
            ->transformWith(new PersonilTransformer())
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->toArray();

        return response()->json($respond);
    }

    public function resetPassword(Request $request)
    {
        $pengaturan = Pengaturan::getByKey('default_password')->first();
        $personil = Personil::find($request->id);

        $auth = $personil->auth ? $personil->auth : $personil->bhabin->auth;

        if (!$auth)
            return response()->json(['error' => 'Personil tidak memiliki akun'], 403);

        $reset = $auth->update([
            'password' => bcrypt($pengaturan->nilai)
        ]);

        if(!$reset) {
            return response()->json(['error' => 'Terjadi kesalahan saat reset password'], 500);
        }

        return response()->json(['success' => true]);
    }

    public function absenPersonil($personil, $request)
    {
        $tanggal = date('Y-m-d');

        //Cek jika hari baru
        $cekHariBaru = Absensi::whereDate('waktu_mulai', $tanggal)
                                ->where('id_personil', $personil->id)
                                ->count();
        //Jika belum ditemukan, absen datang
        if($cekHariBaru == 0) {
            $this->absenDatang($personil, $request);
        }
        else {
            $this->absenPulang($personil, $request);
        }
    }

    public function absenDatang($personil, $request)
    {
        if($request['id_dinas'] != '9') {
            Absensi::create([
                'id_personil' => $personil->id,
                'waktu_mulai' => \Carbon\Carbon::now(),
                'lat_datang' => $request['lat'],
                'lng_datang' => $request['lng'],
                'waktu_selesai' => null,
            ]);
        }
    }

    public function absenPulang($personil, $request)
    {
        $tanggal = date('Y-m-d');

        $absensi = Absensi::whereDate('waktu_mulai', $tanggal)
                                ->where('id_personil', $personil->id)
                                ->first();
        
        if($request['id_dinas'] == '9' && is_null($absensi->waktu_selesai)) {
            $absensi->update([
                'lat_pulang' => $request['lat'],
                'lng_pulang' => $request['lng'],
                'waktu_selesai' => \Carbon\Carbon::now(),
            ]);
        }
    }

    public function patroli(Request $request)
    {
        $user = $request->user();
        list($orderBy, $direction) = explode(':', $request->sort);

        if (!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Anda tidak memiliki aksess di halaman ini'], 403);
        $paginator = $request->filter == '' ?
            LogPersonil::where('status_dinas', 2)
            ->orderBy($orderBy, $direction)
            ->paginate(10) :
            LogPersonil::searchStatus($request->filter, 2)
            ->where('status_dinas', 2)
            ->orderBy($orderBy, $direction)
            ->paginate(10);

        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(new LogPersonilTransformer())
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function pengawalan(Request $request)
    {
        $user = $request->user();
        list($orderBy, $direction) = explode(':', $request->sort);

        if (!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Anda tidak memiliki aksess di halaman ini'], 403);
        $paginator = $request->filter == '' ?
            LogPersonil::where('status_dinas', 3)
            ->orderBy($orderBy, $direction)
            ->paginate(10) :
            LogPersonil::searchStatus($request->filter, 3)
            ->where('status_dinas', 3)
            ->orderBy($orderBy, $direction)
            ->paginate(10);

        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(new LogPersonilTransformer())
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function delete($id)
    {
        $personil = Personil::find($id);
        $user = isset($personil->bhabin) ? $personil->bhabin->auth : $personil->auth;

        if(!$personil->delete()) {
            return response()->json(['error' => 'terjadi kesalahan saat menghapus data'], 500);
        }
        
        if($user) { $user->delete(); }
    }
}
