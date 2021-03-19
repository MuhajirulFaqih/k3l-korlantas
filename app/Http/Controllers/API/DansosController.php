<?php

namespace App\Http\Controllers\API;

use App\Models\DansosLaporan;
use App\Models\DansosPagu;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\DansosLaporanTransformer;
use App\Transformers\DansosPaguTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class DansosController extends Controller
{
    public function getPagu(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin', 'admin']))
            return response()->json(['error' => 'Terlarang'], 403);

        $pagination = DansosPagu::where('id_kel', $request->id_kel)->orderBy('created_at', 'desc')->paginate(10);
        $collection = $pagination->getCollection();


        return fractal()
            ->collection($collection)
            ->transformWith(DansosPaguTransformer::class)
            ->parseIncludes('kelurahan')
            ->paginateWith(new IlluminatePaginatorAdapter($pagination))
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }

    public function tambahPagu(Request $request){
        $user = $request->user();

        if ($user->jenis_pemilik != 'bhabin' && !in_array($request->id_kel, $user->pemilik->personil->kelurahan->pluck('id_kel')->all()))
            return response()->json(['error' => 'Terlarang'], 403);

        $validatedData = $request->validate([
            'id_kel' => 'required',
            'pagu' => 'required|numeric',
            'tahun_anggaran' => 'required|numeric',
            'asal' => 'required'
        ]);

        $pagu = DansosPagu::create($validatedData);

        if (!$pagu)
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        return response()->json(['success' => true, 'id' => $pagu->id]);
    }

    public function ubahPagu(Request $request, DansosPagu $dansosPagu){
        $user = $request->user();

        if ($user->jenis_pemilik != 'bhabin' && !in_array($dansosPagu->id_kel, $user->pemilik->personil->kelurahan->pluck('id_kel')->all()))
            return response()->json(['error' => 'Terlarang'], 403);

        $validatedData = $request->validate([
            'pagu' => 'required',
            'tahun_anggaran' => 'required',
            'asal' => 'required'
        ]);

        if (!$dansosPagu->update($validatedData))
            return response()->json(['error' => 'Terlarang'], 500);

        return response()->json(['success' => true]);
    }

    public function deletePagu(Request $request, DansosPagu $dansosPagu){
        $user = $request->user();

        if ($user->jenis_pemilik != 'bhabin' && !in_array($dansosPagu->id_kel, $user->pemilik->personil->kelurahan->pluck('id_kel')->all()))
            return response()->json(['error' => 'Terlarang'], 403);

        if (!$dansosPagu->delete())
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        return response()->json(['success' => true]);
    }



    public function getLaporan(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik , ['personil', 'bhabin', 'admin']))
            return response()->json(['error' => 'Terlarang'], 403);

        $paginator = DansosLaporan::where('id_kel', $request->id_kel)->orderBy('created_at', 'desc')->paginate(10);
        $collection = $paginator->getCollection();


        return fractal()
            ->collection($collection)
            ->parseIncludes('kelurahan')
            ->transformWith(DansosLaporanTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function tambahLaporan(Request $request){
        $user = $request->user();

        if ($user->jenis_pemilik != 'bhabin' && !in_array($request->id_kel, $user->pemilik->personil->kelurahan->pluck('id_kel')->all()))
            return response()->json(['error' => 'Terlarang'], 403);

        $validatedData = $request->validate([
            'id_kel' => 'required',
            'jumlah' => 'required|numeric',
            'kegunaan' => 'required',
            'kepada' => 'required',
            'foto' => 'required|image',
            'tahun_anggaran' => 'required|numeric'
        ]);

        $validatedData['foto'] = $validatedData['foto']->storeAs(
            'dansos', str_random(40).'.'.$validatedData['foto']->extension()
        );

        $laporan = DansosLaporan::create($validatedData);


        if (!$laporan)
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        return response()->json(['success' => true, 'id' => $laporan->id]);
    }

    public function ubahLaporan(Request $request, DansosLaporan $dansosLaporan){
        $user = $request->user();

        if ($user->jenis_pemilik != 'bhabin' && !in_array($dansosLaporan->id_kel, $user->pemilik->personil->kelurahan->pluck('id_kel')->all()))
            return response()->json(['error' => 'Terlarang'], 403);

        $validatedData = $request->validate([
            'jumlah' => 'required|numeric',
            'kegunaan' => 'required',
            'kepada' => 'required',
            'foto' => 'nullable|image',
            'tahun_anggaran' => 'required|numeric'
        ]);

        $update = [
            'jumlah' => $validatedData['jumlah'],
            'kegunaan' => $validatedData['kegunaan'],
            'kepada' => $validatedData['kepada'],
            'tahun_anggaran' => $validatedData['tahun_anggaran']
        ];

        if ($request->file('foto'))
            $update['foto'] = $validatedData['foto']->storeAs(
                'dansos', str_random(40).'.'.$validatedData['foto']->extension()
            );

        if (!$dansosLaporan->update($update))
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        return response()->json(['success' => true]);
    }

    public function deleteLaporan(Request $request, DansosLaporan $dansosLaporan){
        $user = $request->user();

        if ($user->jenis_pemilik != 'bhabin' && !in_array($dansosLaporan->id_kel, $user->pemilik->personil->kelurahan->pluck('id_kel')->all()))
            return response()->json(['error' => 'Terlarang'], 403);

        if(!$dansosLaporan->delete())
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        return response()->json(['success' => true]);
    }
}
