<?php

namespace App\Http\Controllers\API;

use App\Events\RejectCallMasyarakatEvent;
use App\Http\Controllers\Controller;
use App\Models\CallLog;
use App\Models\Masyarakat;
use App\Models\User;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\MasyarakatTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class MasyarakatController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        list($orderBy, $direction) = explode(':', $request->sort);

        if (!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Anda tidak memiliki akses di halaman ini'], 403);

        $paginator = $request->filter == '' ?
            Masyarakat::orderBy($orderBy, $direction)->paginate(10) :
            Masyarakat::filtered($request->filter)
            ->orderBy($orderBy, $direction)
            ->paginate(10);

        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(new MasyarakatTransformer())
            ->parseIncludes(['kelurahan'])
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        if (!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Anda tidak memiliki akses ke halaman ini'], 403);
        if(is_null($request->nik) || $request->nik == '' || $request->nik == 'null') {
            $validatedData = $request->validate([
                'nama'         => 'required',
                'alamat'         => 'required',
                'no_telp'         => 'required|numeric',
                // 'id_kel'         => 'required|numeric',
                'fotos'       => 'nullable|image',
            ]);
        } else {
            $validatedData = $request->validate([
                'nik'      => "required|unique:masyarakat,nik,{$request->id},id|numeric",
                'nama'         => 'required',
                'alamat'         => 'required',
                'no_telp'         => 'required|numeric',
                // 'id_kel'         => 'required|numeric',
                'fotos'       => 'nullable|image',
            ]);
        }

        $masyarakat = Masyarakat::find($id);

        if (isset($validatedData['fotos'])) {
            $foto = $validatedData['fotos']
                                   ->storeAs('fotos', $user->id. '_' . Str::random(40).'.'.
                                   $validatedData['fotos']->extension());
            Storage::delete($masyarakat->foto);
        }
        else {
            $foto = $masyarakat->foto;
        }

        $masyarakat->update([
            'nik'      => is_null($request->nik) || $request->nik == '' || $request->nik == 'null' ? null : $request->nik,
            'nama'         => $request->nama,
            'alamat'         => $request->alamat,
            'no_telp'         => $request->no_telp,
            // 'id_kel'         => $request->id_kel,
            'foto'       => $foto,
        ]);

        if(!$masyarakat) {
            return response()->json(['error' => 'terjadi kesalahan saat mengedit data'], 500);
        }
    }

    public function delete($id)
    {
        $masyarakat = Masyarakat::find($id);
        $user = isset($masyarakat->auth) ? $masyarakat->auth : false;
        if($user) { $user->delete(); }
        if(!$masyarakat->delete()) {
            return response()->json(['error' => 'terjadi kesalahan saat menghapus data'], 500);
        }
    }

    public function show(Request $request, Masyarakat $masyarakat){
        $user = $request->user();

        if ($user->jenis_pemilik !== 'admin')
            return response()->json(['error' => 'Terlarang'], 403);

        return fractal()
            ->item($masyarakat)
            ->parseIncludes('auth')
            ->transformWith(MasyarakatTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }

    public function rejectCall(Request $request){
        $user = $request->user();

        if ($user->jenis_pemilik !== 'masyarakat')
            return response()->json(['error' => 'Terlarang'], 403);

        $admin = User::where('username', $request->username)->first();

        if (!$admin)
            return response()->json(['error' => 'User tidak ditemukan'], 404);

        broadcast(new RejectCallMasyarakatEvent($admin->id));

        return response()->json(['success' => true]);
    }

    public function cancelCall(Request $request){
        $user = $request->user();

        if ($user->jenis_pemilik !== 'admin')
            return response()->json(['error' => 'Terlarang'], 403);

        $masyarakat = Masyarakat::find($request->id_masyarakat);

        if (!$masyarakat)
            return response()->json(['error' => 'Masyarakat tidak ditemukan'], 404);

        $to = $masyarakat->auth;

        if (!$to)
            return response()->json(['error' => 'Masyarakat tidak ditemukan'], 404);

        if (env('USE_ONESIGNAL', false))
            $this->kirimNotifikasiViaOnesignal('cancel-call', ['pesan' => 'Incoming call '. $user->username,'from' => $user->pemilik->nama, 'from_user' => $user->username, 'to_user' => $to->username], [$to->id]);

        $this->kirimNotifikasiViaGcm('cancel-call', ['pesan' => 'Cancel call '. $user->username,'from' => $user->pemilik->nama, 'from_user' => $user->username, 'to_user' => $to->username], [$to->fcm_id]);
    }

    public function call(Request $request){
        $user = $request->user();

        if($user->jenis_pemilik !== 'admin')
            return response()->json(['error' => 'Terlarang'], 403);

        $masyarakat = Masyarakat::find($request->id_masyarakat);

        if (!$masyarakat)
            return response()->json(['error' => 'Masyarakat tidak ditemukan'], 404);


        $to = $masyarakat->auth;

        if (!$to)
            return response()->json(['error' => 'Masyarakat tidak ditemukan'], 404);

        $log = CallLog::create([
            'from' => $user->username,
            'to' => $to->username,
            'id_from' => $user->id,
            'id_to' => $to->id
        ]);


        if(!$log)
            return response()->json(['error' => 'terjadi kesalahan'], 500);

        if (env('USE_ONESIGNAL', false))
            $this->kirimNotifikasiViaOnesignal('incoming-call', ['pesan' => 'Incoming call '. $user->username,'from' => $user->pemilik->nama, 'from_user' => $user->username, 'to_user' => $to->username], [$to->id]);

        $this->kirimNotifikasiViaGcm('incoming-call', ['pesan' => 'Incoming call '. $user->username,'from' => $user->pemilik->nama, 'from_user' => $user->username, 'to_user' => $to->username], [$to->fcm_id]);

        return response()->json(['success' => true, 'id' => $log->id]);
    }

    public function ambilSemua(Request $request){
        $user = $request->user();

        if($user->jenis_pemilik !== 'admin')
            return response()->json(['error' => 'Terlarang'], 403);

        $masyarakat = Masyarakat::get();
        return fractal()
            ->collection($masyarakat)
            ->transformWith(MasyarakatTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }
}
