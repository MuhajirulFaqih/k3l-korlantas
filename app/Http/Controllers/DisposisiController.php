<?php

namespace App\Http\Controllers;

use App\Models\Personil;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Models\Surat;
use App\Models\SuratDisposisi;
use App\Models\SuratDisposisiTujuan;
use App\Models\SuratJenisAsal;
use App\Transformers\SuratTransformer;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class DisposisiController extends Controller
{
    public function index(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'personil'])){
            return response()->json(['error' => 'Terlarang'], 403);
        }

        list($order, $dir) = explode(':', $request->sort ?? 'created_at:desc');

        if ($user->jenis_pemilik == 'admin'){
            $query = Surat::filter($request->filter)->orderBy($order, $dir);
        } else {
            $query = Surat::filter($request->filter)->whereHas('tujuan', function ($q) use ($user) {
                $q->where('id_jabatan', optional($user->pemilik)->id_jabatan);
            })->orderBy($order, $dir);
        }

        $paginator = $query->paginate(10);
        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(SuratTransformer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }

    public function ubahSurat(Request $request, Surat $surat){
        $user = $request->user();
        if ($user->jenis_pemilik != 'admin')
            return response()->json(['error' => 'Terlarang'], 403);

        $validData = $request->validate([
            'tanggal' => 'required|date',
            'waktu_diterima' => 'required|date',
            'id_asal' => 'required|numeric',
            'no_agenda' => 'required|min:3',
            'nomor' => 'required|min:3',
            'pengirim' => 'required|min:3',
            'derajat' => 'required|in:segera,biasa,kilat',
            'klasifikasi' => 'required|in:biasa,rahasia,telegram_rahasia,telegram,undangan,surat_dinas,sprin,skep',
            'perihal' => 'required|min:8',
            'file' => 'nullable'
        ]);

        $validData['waktu_diterima'] = Carbon::createFromFormat('d-m-Y H:i:s', $validData['waktu_diterima']);

        if (isset($validData['file']) && !$validData['file'])
            unset($validData['file']);

        if (!$surat->update($validData))
            return response()->json(['error' => 'Terjadi Kesalahan'], 500);

        return response()->json(['success' => true]);
    }

    public function buat(Request $request){
        $user = $request->user();

        if ($user->jenis_pemilik != 'admin')
            return response()->json(['error' => 'Terlarang'], 4030);


        $validData = $request->validate([
            'tanggal' => 'required|date',
            'waktu_diterima' => 'required|date',
            'id_asal' => 'required|numeric',
            'no_agenda' => 'required|min:3',
            'nomor' => 'required|min:3',
            'pengirim' => 'required|min:3',
            'derajat' => 'required|in:segera,biasa,kilat',
            'klasifikasi' => 'required|in:biasa,rahasia',
            'perihal' => 'required|min:8',
            'file' => 'required'
        ]);

        $validData['waktu_diterima'] = Carbon::createFromFormat('d-m-Y H:i:s', $validData['waktu_diterima']);

        $surat = Surat::create($validData);

        if (!$surat)
            return response()->json(['error' => 'Terjadi kesalahan'], 500);



        return response()->json(['success' => true]);
    }

    public function hapus(Request $request, Surat $surat){
        $user = $request->user();

        if($user->jenis_pemilik != 'admin')
            return response()->json(['error' => 'Terlarang']);


        try {
            DB::beginTransaction();

            $tujuan = $surat->tujuan();
            $disposisi = $surat->disposisi();

            $tujuan->delete();
            $disposisi->delete();
            $surat->delete();

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Terjadi kesalahan'], 500);
        }
    }

    public function detail(Request $request, Surat $surat){
        if (!$surat)
            return response()->json(['error' => 'Not Found'], 404);

        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil', 'admin']))
            return response()->json(['error' => 'Terlarang'], 403);

        if ($user->jenis_pemilik == 'personil' && !in_array($user->pemilik->id_jabatan, $surat->jabatan->pluck('id')->all()))
            return response()->json(['error' => 'Terlarang'], 403);

        return fractal()
            ->item($surat)
            ->transformWith(SuratTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }

    public function jenis(Request $request){
        $user = $request->user();

        if ($user->jenis_pemilik != 'admin')
            return response()->json(['error' => 'Terlarang'], 403);

        $jenis = SuratJenisAsal::get();

        return response()->json($jenis);
    }

    public function upload(Request $request){
        $user = $request->user();

        if ($user->jenis_pemilik != 'admin')
            return response()->json(['error' => 'Terlarang'], 403);

        $validateData = $request->validate([
            'file' => 'required|file',
            'jenis' => 'required'
        ]);

        $file = $validateData['file']->storeAs(
            $validateData['jenis'],
            Str::random(40). '.'.$validateData['file']->extension()
        );

        return response()->json(['success' => true, 'file' => $file]);
    }

    public function tambahDisposisi(Request $request, Surat $surat){
        $user = $request->user();

        if ($user->jenis_pemilik != 'admin')
            return response()->json(['error' => 'Terlarang'], 403);

        $validateData = $request->validate([
            //'w_disposisi' => 'required|date_format:Y-m-d H:i:s',
            'isi' => 'required|min:8',
            'file' => 'required',
            'id_jabatan.*' => 'required|numeric'
        ]);

        $file = null;

        try {
            DB::beginTransaction();

            $disposisi = $surat->disposisi()->create([
                'w_disposisi' => Carbon::now(),
                'isi' => $validateData['isi'],
                'file' => $validateData['file']
            ]);

            if (!$disposisi)
                return response()->json(['error' => 'Terjadi kesalahan'], 500);


            foreach ($validateData['id_jabatan'] as $row){
                SuratDisposisiTujuan::create([
                    'id_surat' => $surat->id,
                    'id_disposisi' => $disposisi->id,
                    'id_jabatan' => $row
                ]);
            }

            DB::commit();

            // Todo Notifikasi

            $personil = Personil::whereIn('id_jabatan', $validateData['id_jabatan'])->get();

            $data = [
                'pesan' => 'Disposisi baru dari pimpinan',
                'id' => $surat->id,
                'isi' => $disposisi->isi
            ];
            if ($personil->count() > 0)
                $this->kirimNotifikasiViaOnesignal('disposisi-baru', $data, $personil->pluck('id')->all());

            return response()->json(['success' => true]);
        } catch (\Exception $e){
            DB::rollBack();
            Log::error('Error', $e->getTrace());
            return response()->json(['error' => 'Terjadi kesalahan', 'msg' => $e->getMessage()], 500);
        }
    }

    public function hapusDisposisi(Request $request, SuratDisposisi $disposisi){
        $user = $request->user();

        if($user->jenis_pemilik != 'admin')
            return response()->json(['error' => 'Terlarang']);


        try {
            DB::beginTransaction();

            $tujuan = $disposisi->tujuan();
            $tujuan->delete();
            $disposisi->delete();

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Terjadi kesalahan', 'msg' => $e->getMessage()], 500);
        }
    }
}
