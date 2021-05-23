<?php

use App\Http\Controllers\API\AbsensiController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\CallController;
use App\Http\Controllers\API\DaruratController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\ExportKejadianController;
use App\Http\Controllers\API\ExportLaporan;
use App\Http\Controllers\API\ExportPengaduanController;
use App\Http\Controllers\API\HTController;
use App\Http\Controllers\API\InformasiController;
use App\Http\Controllers\API\JabatanController;
use App\Http\Controllers\API\JenisController;
use App\Http\Controllers\API\KegiatanController;
use App\Http\Controllers\API\KejadianController;
use App\Http\Controllers\API\KesatuanController;
use App\Http\Controllers\API\LogPersonilController;
use App\Http\Controllers\API\MasyarakatController;
use App\Http\Controllers\API\NewsController;
use App\Http\Controllers\API\PangkatController;
use App\Http\Controllers\API\PengaduanController;
use App\Http\Controllers\API\PersonilController;
use App\Http\Controllers\API\TempatVitalController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\MonitController;
use App\Http\Controllers\API\PengumumanController;
use App\Http\Controllers\API\DinasController;
use App\Http\Controllers\API\TitikApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

// User Routes
Route::prefix('user')->group(function () {
    Route::post('registrasi', [UserController::class, 'register']);
    Route::post('otp', [UserController::class, 'kode_verifikasi']);
    Route::as('login')->post('auth', [AuthController::class, 'issueToken']);
    Route::post('auth/social', [UserController::class, 'loginSocialMedia']);
    Route::post('auth-admin', [AuthController::class, 'issueTokenAdmin']);
    Route::post('reset_password', [UserController::class, 'resetPassword']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/', [UserController::class, 'details']);
        Route::post('fcm_id', [UserController::class, 'fcm']);
        Route::post('ubah-nik', [UserController::class, 'ubahNik']);
        Route::post("ubah_telp", [UserController::class, 'ubahNomor']);
        Route::post("ubah-telp-o", [UserController::class, 'ubahNomorO']);
        Route::post('ubah_password', [UserController::class, 'change_password']);
        Route::post('ubah-password-admin', [UserController::class, 'change_password_admin']);
        Route::post('update_pp', [UserController::class, 'updatePP']);
        Route::post('ubah_profil', [UserController::class, 'change_profil']);
        Route::get('sms_otp/androido', [UserController::class, 'smsOtpO']);
        Route::post('lacak', [UserController::class, 'tracking']);
        Route::post('lacak-masyarakat', [UserController::class, 'trackingMasyarakat']);
        Route::get('dinas', [UserController::class, 'getDinas']);
        Route::post('dinas', [PersonilController::class, 'updateStatusDinas']);
        Route::get('htchannels', [HTController::class, 'getAllChannels']);
        Route::get('channels', [HTController::class, 'getByKesatuan']);
        Route::get('logout', [UserController::class, 'logout']);
        Route::get('logout-admin', [UserController::class, 'logoutAdmin']);
    });
});
// END USER ROUTES

Route::middleware('auth:api')->group(function () {

    Route::get('set', function () {
        $htHost = base64_encode(base64_encode(env("HT_HOST")));
        $htPass = base64_encode(base64_encode(env("HT_PASS")));
        $vcWss = base64_encode(base64_encode(env("VC_WSS_URL")));
        $vcNatServer = base64_encode(base64_encode(env("VC_NAT_SERVER")));

        $response = ['1' => $htHost, '2' => $htPass, '3' => $vcWss, '4' => $vcNatServer];

        return response()->json($response);
    });

    // HT ROUTES
    Route::prefix('ht')->group(function () {
        Route::get('/', [HTController::class, 'index']);

        Route::post('/', [HTController::class, 'store']);

        Route::post('/{ht}', [HTController::class, 'update']);
        Route::delete('/{ht}', [HTController::class, 'delete']);
    });
// END OF HT ROUTES

    Route::prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index']);
    });

    Route::prefix('informasi')->group(function () {
        Route::get('/', [InformasiController::class, 'getAll']);

        Route::post('/', [InformasiController::class, 'tambah']);

        Route::get('aktif', [InformasiController::class, 'getAktif']);

        Route::post('{informasi}', [InformasiController::class, 'ubahInformasi']);
    });

    Route::prefix('monit')->group(function () {
        Route::get('/', [MonitController::class, 'index']);
    });

// Kesatuan
    Route::prefix('kesatuan')->group(function () {
        Route::get('/', [KesatuanController::class, 'ambilSemua']);

        Route::get('/all', [KesatuanController::class, 'index']);

        Route::post('/', [KesatuanController::class, 'store']);

        Route::post('/{kesatuan}', [KesatuanController::class, 'update']);
        Route::delete('/{kesatuan}', [KesatuanController::class, 'delete',]);
    });
// endof kesatuan

// Jabatan
    Route::prefix('jabatan')->group(function () {
        Route::get('/', [JabatanController::class, 'ambilSemua']);
    });
// endof kesatuan

// Pangkat
    Route::group([
        'prefix' => 'pangkat',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('/', [PangkatController::class, 'ambilSemua']);
    });
// endof kesatuan

    Route::prefix('kegiatan')->group(function () {
        Route::get('/', [KegiatanController::class, 'list_laporan']);

        Route::post('/', [KegiatanController::class, 'upload_laporan']);

        Route::get('tipejenis', [KegiatanController::class, 'getJenisTipe']);
        
        Route::get('tipejenisbypersonil', [KegiatanController::class, 'getJenisTipeByPersonil']);

        Route::get('{kegiatan}', [KegiatanController::class, 'detail_laporan']);

        Route::get('{kegiatan}/komentar', [KegiatanController::class, 'getKomentar']);

        Route::post('{kegiatan}/komentar', [KegiatanController::class, 'tambahKomentar']);

    });

    Route::prefix('pengumuman')->group(function () {
        Route::get('/', [PengumumanController::class, 'index']);
        Route::post('/', [PengumumanController::class, 'store']);
        Route::get('{pengumuman}', [PengumumanController::class, 'show']);
        Route::delete('{pengumuman}', [PengumumanController::class, 'delete']);
    });

    Route::prefix('kejadian')->group(function () {

        Route::get('/', [KejadianController::class, 'listkejadian']);
        Route::post('/', [KejadianController::class, 'create_kejadian']);

        Route::get('detail', [KejadianController::class, 'getDetail']);

        Route::get('/total', [KejadianController::class, 'total']);

        Route::get('{kejadian}', [KejadianController::class, 'detailkejadian']);

        Route::post('{kejadian}/unfollow', [KejadianController::class, 'unfollow']);

        Route::post('{kejadian}/komentar', [KejadianController::class, 'postkomentar']);

        Route::post('{kejadian}/tindaklanjut', [KejadianController::class, 'buatTindakLanjut']);

        Route::post('/broadcast', [KejadianController::class, 'broadcast']);
    });

    Route::prefix('pengaduan')->group(function () {
        Route::get('/', [PengaduanController::class, 'getAll']);

        Route::post('/', [PengaduanController::class, 'tambah']);

        Route::get('{pengaduan}', [PengaduanController::class, 'lihat']);

        Route::get('{pengaduan}/komentar', [PengaduanController::class, 'lihatKomentar']);

        Route::post('{pengaduan}/komentar', [PengaduanController::class, 'buatKomentar']);
    });

//Call
    Route::prefix('call')->group(function () {
        Route::post('request-to-admin', [CallController::class, 'createCallFromPersonil']);
        Route::post('request', [CallController::class, 'createCall']);

        Route::get('ready', [CallController::class, 'ready']);

        Route::post('update', [CallController::class, 'updateCall']);

        Route::get('history', [CallController::class, 'getCall']);
    });

// END KEJADIAN GROUP

// Darurat
    Route::prefix('darurat')->group(function () {
        Route::get('/', [DaruratController::class, 'lihatSemua']);

        Route::get('{darurat}', [DaruratController::class, 'lihat']);

        Route::post('{darurat}/selesai', [DaruratController::class, 'selesai']);

        Route::post('/', [DaruratController::class, 'tambah']);
    });
// End Darurat

// Berita
    Route::prefix('news')->group(function () {
        Route::get('/', [NewsController::class, 'semua']);

        Route::get('/slide', [NewsController::class, 'slide']);

        Route::get('{news}', [NewsController::class, 'get']);
    });

    Route::prefix('status-dinas')->group(function () {
        Route::get('/', [DinasController::class, 'index']);
    });


// pengaturan
    /*Route::prefix('pengaturan')->group(function () {
        Route::get('/', [
            'uses' => 'PengaturanController@load'
        ]);
        Route::get('/notif', [
            'uses' => 'PengaturanController@notif'
        ]);
        Route::post('/banner_grid', [
            'uses' => 'PengaturanController@bannerGrid'
        ]);
        Route::post('pdf_sispammako', [
            'uses' => 'PengaturanController@pdfSispammako'
        ]);
        Route::post('default_password', [
            'uses' => 'PengaturanController@defaultPassword'
        ]);
        Route::post('pdf_visi_misi', [
            'uses' => 'PengaturanController@pdfVisiMisi'
        ]);
        Route::post('pdf_program_kapolres', [
            'uses' => 'PengaturanController@pdfProgramKapolres'
        ]);
        Route::post('pdf_kebijakan_kapolres', [
            'uses' => 'PengaturanController@pdfKebijakanKapolres'
        ]);
        Route::post('auto_send_notification', [
            'uses' => 'PengaturanController@autoSendNotification'
        ]);
    });*/
// pengaturan

    // PERSONIL
    Route::prefix('personil')->group(function () {
        Route::get('tracking', [PersonilController::class, 'tracking']);
        Route::get('pengawalan', [PersonilController::class, 'pengawalan']);
        Route::get('patroli', [PersonilController::class, 'patroli']);
        Route::get('fetch', [PersonilController::class, 'fetchPerosonil']);
        Route::get('/', [PersonilController::class, 'index']);
        Route::post('/', [PersonilController::class, 'tambah']);
        Route::get('{personil}/ptt', [PersonilController::class, 'ubahPttHt']);
        Route::delete('/{personil}', [PersonilController::class, 'delete']);
        Route::post('foto', [PersonilController::class, 'uploadFoto']);
        Route::get('{personil}', [PersonilController::class, 'lihat']);
        Route::post('{personil}/edit', [PersonilController::class, 'edit']);
        Route::get('lihat/tracking', [PersonilController::class, 'tracking']);
        Route::post('reset_password', [PersonilController::class, 'resetPassword']);
    });

    // Masyarakat
    Route::prefix('masyarakat')->group(function () {
        Route::get('/', [MasyarakatController::class, 'index']);
        Route::post('call', [MasyarakatController::class, 'call']);
        Route::post('cancel-call', [MasyarakatController::class, 'cancelCall']);
        Route::post('reject-call', [MasyarakatController::class, 'rejectCall']);
        Route::get('{masyarakat}', [MasyarakatController::class, 'show']);
        Route::post('/{kesatuan}', [MasyarakatController::class, 'update']);
        Route::delete('/{kesatuan}', [MasyarakatController::class, 'delete']);
    });
    // endof masyarakat

    // JENIS
    Route::prefix('jenis')->group(function () {
        Route::get('/', [JenisController::class, 'index']);
    });
//JENIS END

// TEMPAT VITAL
    Route::prefix('tempat-vital')->group(function () {
        Route::get('jenis/{id}', [TempatVitalController::class, 'ByJenis']);
        Route::post('all', [TempatVitalController::class, 'getAll']);
    });
//TEMPAT VITAL END

// Form Laporan
    Route::prefix('form')->group(function () {
        Route::get('tipe-laporan', [ExportLaporan::class, 'selectTipe']);
        Route::get('jenis-giat', [ExportLaporan::class, 'jenisGiat']);
    });
//end Form

    Route::prefix('export-laporan')->group(function () {
        Route::get('show-data', [ExportLaporan::class, 'index']);
        Route::post('show-data', [ExportLaporan::class, 'index']);
        Route::POST('download/{option}', [ExportLaporan::class, 'exportExcelbyChecklist']);
    });

    /*Route::prefix('export-laporan-masyarakat')->group(function () {
        Route::get('show-data', 'API\ExportLaporanMasyarakat@index');
        Route::post('show-data', 'API\ExportLaporanMasyarakat@index');
        Route::post('download', 'API\ExportLaporanMasyarakat@exportExcelbyChecklist');
    });*/

    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
    });

    Route::prefix('check-updates')->group(function () {
        Route::get('internal', function () {
            $latestVersion = (\App\Models\Pengaturan::where('nama', 'latest_version_internal')->first())->nilai;
            $latestVersionCode = (\App\Models\Pengaturan::where('nama', 'latest_version_code_internal')->first())->nilai;
            $url = (\App\Models\Pengaturan::where('nama', 'url_internal')->first())->nilai;
            $releaseNote = json_decode((\App\Models\Pengaturan::where('nama', 'release_note_internal')->first())->nilai);

            return response()->json(compact('latestVersion', 'latestVersionCode', 'url', 'releaseNote'));
        });

        Route::get('external', function () {
            $latestVersion = (\App\Models\Pengaturan::where('nama', 'latest_version_external')->first())->nilai;
            $latestVersionCode = (\App\Models\Pengaturan::where('nama', 'latest_version_code_external')->first())->nilai;
            $url = (\App\Models\Pengaturan::where('nama', 'url_external')->first())->nilai;
            $releaseNote = json_decode((\App\Models\Pengaturan::where('nama', 'release_note_external')->first())->nilai);

            return response()->json(compact('latestVersion', 'latestVersionCode', 'url', 'releaseNote'));
        });
    });

    Route::get('provinsi', function () {
        $provinsi = \App\Models\Provinsi::with(['kabupaten'])->get();
        return response()->json(['provinsi' => $provinsi]);
    });

    Route::get('kabupaten-provinsi', function () {
        $where = env('TINGKAT_DANA_DESA', 'provinsi') == 'kabupaten' ? 'id_kab' : 'id_prov';
        $kabupaten = \App\Models\Kabupaten::where($where, env('PROVINSI_DANA_DESA'))->get();
        return response()->json(['kabupaten' => $kabupaten]);
    });

    Route::get('kecamatan/{kabupaten}', function ($id) {
        $kecamatan = \App\Models\Kecamatan::where('id_kab', $id)->get();
        return response()->json(['kecamatan' => $kecamatan]);
    });

    Route::get('kelurahan/{kecamatan}', function ($id) {
        $kelurahan = \App\Models\Kelurahan::where('id_kec', $id)->get();
        return response()->json(['kelurahan' => $kelurahan]);
    });

    Route::get('wilayah', function () {
        $id_kab = explode(',', env('APP_KAB'));
        $kecamatan = App\Models\Kecamatan::whereIn('id_kab', $id_kab)->orderBy('nama', 'asc')->get();

        return fractal()
            ->collection($kecamatan)
            ->parseIncludes('kelurahan')
            ->transformWith(App\Transformers\KecamatanTransformer::class)
            ->serializeWith(App\Serializers\DataArraySansIncludeSerializer::class)
            ->respond();
    });

    Route::get('wilayahkab', function () {
        $id_kab = explode(',', env('APP_KAB'));
        $kecamatan = App\Models\Kabupaten::whereIn('id_kab', $id_kab)->orderBy('nama', 'asc')->get();

        return fractal()
            ->collection($kecamatan)
            ->parseIncludes('kecamatan.kelurahan')
            ->transformWith(App\Transformers\KabupatenTransformer::class)
            ->serializeWith(App\Serializers\DataArraySansIncludeSerializer::class)
            ->respond();
    });

    Route::get('get-log-personil', [LogPersonilController::class, 'index']);

    Route::prefix('absensi')->group(function () {
        Route::get('/', [AbsensiController::class, 'index']);
        Route::post('/', [AbsensiController::class, 'absenPersonil']);
        Route::get('/today', [AbsensiController::class, 'getToday']);
        Route::post('/export', [AbsensiController::class, 'export']);
    });

    if (env('APP_ENV') !== 'production') {
        Route::post('default_banner', function (Request $request) {
            $banner = $request->banner->storeAs(
                'banner_grid',
                Str::random(40) . '.' . $request->banner->extension()
            );

            $pengaturan = App\Models\Pengaturan::getByKey('default_banner_grid')->first();
            $pengaturan->nilai = $banner;

            if ($pengaturan->save())
                return response()->json(['berhasil']);

            return response()->json(['error']);
        });
    }


    Route::prefix('export-kejadian')->group(function () {
        Route::get('/', [ExportKejadianController::class, 'index']);
        Route::post('/cetak', [ExportKejadianController::class, 'cetak']);
    });

    Route::group([
        'prefix' => 'export-pengaduan',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('/', [ExportPengaduanController::class, 'index']);
        Route::post('/cetak', [ExportPengaduanController::class, 'cetak']);
    });
});

Route::post('news', [NewsController::class, 'tambah']);

Route::get('jenis-kejadian', function (){
    $kejadian = explode(',', env('LIST_KEJADIAN', ''));

    return response()->json($kejadian);
});

Route::group([
    'prefix' => 'titik-api',
    'middleware' => ['auth:api']
], function (){
    Route::get('/', [TitikApiController::class, 'index']);
    Route::get('/detail/{hotspot}', [TitikApiController::class, 'hotspot']);
});
// End of titik api

Route::get('upload/{pathA}/{pathB}/{pathC?}', function ($pathA, $pathB, $pathC = null) {
    $path = "{$pathA}/{$pathB}";
    if ($pathC !== null) $path .= "/{$pathC}";
    $mime = \Illuminate\Support\Facades\Storage::mimeType($path);
    $allowedMime = ['comm/jpeg', 'image/png', 'image/gif', 'application/pdf', 'image/svg+xml', 'image/jpeg'];

    if (!in_array($mime, $allowedMime))
        return response()->json(['error' => 'Tidak terpenuhi.'], 400);
    else
        return response()->file(storage_path("app/{$path}"));
});

