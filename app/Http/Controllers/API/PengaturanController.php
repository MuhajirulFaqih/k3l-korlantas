<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaturan;

class PengaturanController extends Controller
{
    public function load(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Terlarang'], 403);

        $pengaturan = Pengaturan::get();

        return response()->json($pengaturan);
    }

    public function notif(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Terlarang'], 403);

        $pengaturan = Pengaturan::getByKey('auto_send_notification')->first();

        return response()->json($pengaturan);
    }

    public function bannerGrid(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Terlarang'], 403);

        $validData = $request->validate([
            'file' => 'required|image'
        ]);

        $defaultBanner = $validData['file']->storeAs(
            'banner_grid',
            str_random(40).'.'.$validData['file']->extension()
        );

        $updatePengaturan = Pengaturan::getByKey('default_banner_grid')->first();
        $updatePengaturan->nilai = $defaultBanner;

        if (!$updatePengaturan->save())
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        return response()->json(['success' => true]);
    }

    public function defaultPassword(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Terlarang'], 403);

        $validData = $request->validate([
            'password' => 'required|min:6'
        ]);

        $pengaturan = Pengaturan::getByKey('default_password')->first();
        $pengaturan->nilai = $validData['password'];

        if (!$pengaturan->save())
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        return response()->json(['success' => true]);
    }

    public function pdfVisiMisi(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Terlarang'], 403);

        $validData = $request->validate([
           'file' => 'required|file'
        ]);

        $pdfVisiMisi = $validData['file']->storeAs(
            'visi_misi',
            str_random(40).'.'.$validData['file']->extension()
        );

        $pengaturan = Pengaturan::getByKey('pdf_visi_misi')->first();
        $pengaturan->nilai = $pdfVisiMisi;

        if (!$pengaturan->save())
            return response()->json(['error' => 'Terjadi Kesalahan'], 500);

        return response()->json(['success' => true]);
    }

    public function pdfKebijakanKapolres(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Terlarang'], 403);

        $validData = $request->validate([
            'file' => 'required|file'
        ]);

        $pdfVisiMisi = $validData['file']->storeAs(
            'kebijakan_kapolres',
            str_random(40).'.'.$validData['file']->extension()
        );

        $pengaturan = Pengaturan::getByKey('pdf_kebijakan_kapolres')->first();
        $pengaturan->nilai = $pdfVisiMisi;

        if (!$pengaturan->save())
            return response()->json(['error' => 'Terjadi Kesalahan'], 500);

        return response()->json(['success' => true]);
    }

    public function pdfProgramKapolres(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Terlarang'], 403);

        $validData = $request->validate([
            'file' => 'required|file'
        ]);

        $pdfVisiMisi = $validData['file']->storeAs(
            'program_kapolres',
            str_random(40).'.'.$validData['file']->extension()
        );

        $pengaturan = Pengaturan::getByKey('pdf_program_kapolres')->first();
        $pengaturan->nilai = $pdfVisiMisi;

        if (!$pengaturan->save())
            return response()->json(['error' => 'Terjadi Kesalahan'], 500);

        return response()->json(['success' => true]);
    }

    public function pdfSispammako(Request $request){
        $user = $request->user();

        // Hanya admin yang memiliki akses
        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Terlarang'], 403);

        $validData = $request->validate([
            'file' => 'required|file'
        ]);

        $defaultPdfSispam = $validData['file']->storeAs(
            'sispammako',
            str_random(40) . '.' . $validData['file']->extension()
        );

        $pengaturan = Pengaturan::getByKey('pdf_sispammako')->first();
        $pengaturan->nilai = $defaultPdfSispam;

        if (!$pengaturan->save())
            return response()->json(['error' => "Terjadi kesalahan"], 500);

        return response()->json(['success' => true]);
    }

    public function autoSendNotification(Request $request){
        $user = $request->user();

        // Hanya admin yang memiliki akses
        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Terlarang'], 403);

        $validData = $request->validate([
            'nilai' => 'required'
        ]);

        $pengaturan = Pengaturan::getByKey('auto_send_notification')->first();
        $pengaturan->nilai = $request->nilai;

        if (!$pengaturan->save())
            return response()->json(['error' => "Terjadi kesalahan"], 500);

        return response()->json(['success' => true]);
    }
}
