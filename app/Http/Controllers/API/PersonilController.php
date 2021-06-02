<?php

namespace App\Http\Controllers\API;

use App\Events\LacakPersonilEvent;
use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Jabatan;
use App\Models\Kesatuan;
use App\Models\LogPersonil;
use App\Models\Pengaturan;
use App\Models\Personil;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Services\PersonilService;
use App\Transformers\LogPersonilTransformer;
use App\Transformers\PersonilTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class PersonilController extends Controller
{
    public function index(Request $request, Jabatan $jabatan)
    {
        $user = $request->user();
        list($orderBy, $direction) = explode(':', $request->sort ?? 'id:asc');

        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Anda tidak memiliki aksess di halaman ini'], 403);

        if ($user->jenis_pemilik == 'kesatuan') {
            $id_kesatuan = Kesatuan::descendantsAndSelf($user->pemilik->id)->pluck('id')->all();
            $paginator = Personil::with('pangkat', 'kesatuan', 'jabatan', 'kesatuan.parent')->whereIn('id_kesatuan', $id_kesatuan)->orderBy($orderBy, $direction)->paginate(10);
        } else {
            $paginator = Personil::with('pangkat', 'kesatuan', 'jabatan', 'kesatuan.parent')->search($request->filter)
                ->orderBy($orderBy, $direction)
                ->paginate(10);
        }

        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection->all())
            ->transformWith(new PersonilTransformer())
            ->serializeWith(new DataArraySansIncludeSerializer())
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
            Str::random(40) . '.jpg'
        );

        if (!$foto)
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        return response()->json(['success' => true, 'foto' => $foto]);
    }

    public function ubahPttHt(Request $request, Personil $personil)
    {
        $user = $request->user();

        if ($user->jenis_pemilik !== 'admin')
            return response()->json(['error' => 'Terlarang'], 403);

        $personil->ptt_ht = !$personil->ptt_ht;

        if (!$personil->save())
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        return response()->json(['success' => true]);
    }
    
    public function ubahBeat(Request $request, Personil $personil)
    {
        $user = $request->user();

        if ($user->jenis_pemilik !== 'admin')
            return response()->json(['error' => 'Terlarang'], 403);

        $personil->is_patroli_beat = $personil->is_patroli_beat == null || $personil->is_patroli_beat == 0 ? 1 : null;

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
            'nrp' => 'required|digits:8|unique:personil',
            'alamat' => 'nullable|min:6',
            'id_jabatan' => 'required|numeric',
            'id_pangkat' => 'required|numeric',
            'id_kesatuan' => 'required|numeric',
            'foto' => 'nullable',
            'id_kelurahan' => 'array'
        ]);

        $exists = Personil::where('nrp', $validData['nrp'])->first();

        if ($exists) {
            $exists->update($validData);
        } else {
            $personil = Personil::create($validData);

            if (!$personil)
                return response()->json(['error' => 'Terjadi kesalahan'], 500);

            if ($request->file('foto'))
                Storage::move($validData['foto'], 'personil/' . $personil->nrp . '.jpg');
            else if ($request->has('foto') && $request->foto) {
                $foto = file_get_contents($request->foto);
                file_put_contents(storage_path('app/personil/' . $personil->nrp . '.jpg'), $foto);
            }

            $pengaturan = Pengaturan::getByKey('default_password')->first();
            $password = bcrypt($pengaturan->nilai);

            $personil->auth()->create(['username' => $personil->nrp, 'password' => $password]);
        }

        return response()->json(['success' => true], 201);
    }

    public function lihat(Request $request, $id)
    {
        $personil = Personil::find($id);
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
            'nrp' => 'required|digits:8',
            'alamat' => 'nullable|min:6',
            'id_jabatan' => 'required|numeric',
            'id_pangkat' => 'required|numeric',
            'id_kesatuan' => 'required|numeric',
            'no_telp' => 'nullable',
            'foto' => 'nullable',
        ]);

        $user = $personil->auth;

        $personil = Personil::find($personil->id);

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
        if ($request->file('foto')) {
            if (Storage::exists('personil/' . $personil->nrp . '.jpg'))
                Storage::delete('personil/' . $personil->nrp . '.jpg');
            Storage::move($validData['foto'], 'personil/' . $personil->nrp . '.jpg');
        } else if ($request->has('foto') && $request->foto) {
            if (Storage::exists('personil/' . $personil->nrp . '.jpg'))
                Storage::delete('personil/' . $personil->nrp . '.jpg');

            $foto = file_get_contents($request->foto);
            file_put_contents(storage_path('app/personil/' . $personil->nrp . '.jpg'), $foto);
        }

        return response()->json(['success' => true]);

    }

    public function update(Request $request)
    {
        $user = $request->user();

        if ($user->jenis_pemilik != 'personil')
            return response()->json(['error' => 'Terlarang'], 403);

        $personil = $user->pemilik;

        $responsePersonil = (new PersonilService())->fetchPersonil($user->pemilik->nrp);

        if ($responsePersonil == null)
            return response()->json(['error' => 'Tidak dapat mendapatkan data'], 404);


        if (isset($responsePersonil['foto_file']) && $responsePersonil['foto_file']) {
            $foto = file_get_contents($responsePersonil['foto_file']);
            file_put_contents(storage_path('app/personil/' . $personil->nrp . '.jpg'), $foto);
        }

        $data = [
            'id_pangkat' => $responsePersonil['id_pangkat'],
            'id_kesatuan' => $responsePersonil['id_kesatuan'],
            'id_jabatan' => $responsePersonil['id_jabatan'],
            'nama' => $responsePersonil['nama'],
            'no_telp' => $responsePersonil['handphone'],
            'kelamin' => $responsePersonil['jenis_kelamin']
        ];

        if (!$personil->update($data))
            return response()->json(['error' => 'Terjadi Kesalahan'], 500);
        return response()->json(['success' => true]);
    }

    public function updateStatusDinas(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil']))
            return response()->json(['error' => 'Anda tidak memiliki aksess di halaman ini'], 403);

        $validatedData = $request->validate([
            'id_dinas' => 'required',
            'lat' => 'required',
            'lng' => 'required'
        ]);

        $personil = $user->jenis_pemilik == 'personil' ? $user->pemilik : $user->pemilik->personil;
        $log = LogPersonil::where('id_personil', $personil->id)->latest()->first();
        $waktu = Carbon::now();
        if ($log) {
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

        // $this->absenPersonil($personil, $validatedData);

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

        $personil->auth->update([
            'password' => bcrypt($pengaturan->nilai)
        ]);

        if (!$personil) {
            return response()->json(['error' => 'Terjadi kesalahan saat reset password'], 500);
        }

        return response()->json(['success' => true]);
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

    public function fetchPerosonil(Request $request)
    {
        $user = $request->user();

        if ($user->jenis_pemilik != 'admin')
            return response()->json(['error' => 'Terlarang'], 403);

        $user = (new PersonilService())->fetchPersonil($request->nrp);

        /*if (!$user)
            return response()->json(['error' => 'Tidak ditemukan'], 404);*/

        return response()->json(['data' => $user]);
    }

    public function delete($id)
    {
        $personil = Personil::find($id);
        $user = $personil->auth;

        if (!$personil->delete()) {
            return response()->json(['error' => 'terjadi kesalahan saat menghapus data'], 500);
        }

        if ($user) {
            $user->delete();
        }
    }
}
