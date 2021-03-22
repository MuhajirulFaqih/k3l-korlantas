<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group([
    'namespace' => 'App\Http\Controllers',
    'prefix' => '/'
], function () {
    // User Routes
    Route::group([
        'prefix' => 'user',
    ], function () {
        Route::get('/', [
            'uses' => 'API\UserController@details',
            'middleware' => ['auth:api']
        ]);

        Route::post('registrasi', [
            'uses' => 'API\UserController@register',
        ]);

        Route::post('registrasi-o', [
            'uses' => 'API\UserController@registerO'
        ]);

        Route::post('otp', [
            'uses' => 'API\UserController@kode_verifikasi'
        ]);

        Route::post('fcm_id', [
            'uses' => 'API\UserController@fcm',
            'middleware' => ['auth:api']
        ]);

        Route::post('ubah-nik', [
            'uses' => 'API\UserController@ubahNik',
            'middleware' => ['auth:api']
        ]);

        Route::post('auth', [
            'uses' => 'AuthController@issueToken',
            'as' => 'login'
        ]);
        
        Route::post('auth-admin', [
            'uses' => 'AuthController@issueTokenAdmin',
        ]);

        Route::post("ubah_telp", [
            'uses' => 'API\UserController@ubahNomor',
            'middleware' => ['auth:api']
        ]);

        Route::post("ubah-telp-o", [
            'uses' => 'API\UserController@ubahNomorO',
            'middleware' => ['auth:api']
        ]);

        Route::post('auth/social', [
            'uses' => 'API\UserController@loginSocialMedia'
        ]);

        Route::post('update', [
            'uses' => 'API\UserController@update',
            'middleware' => ['auth:api']
        ]);

        Route::post('ubah_password', [
            'uses' => 'API\UserController@change_password',
            'middleware' => ['auth:api']
        ]);
        Route::post('ubah-password-admin', [
            'uses' => 'API\UserController@change_password_admin',
            'middleware' => ['auth:api']
        ]);

        Route::post('reset_password', [
            'uses' => 'API\UserController@resetPassword'
        ]);

        Route::post('update_pp', [
            'uses' => 'API\UserController@updatePP',
            'middleware' => ['auth:api']
        ]);

        Route::post('ubah_profil', [
            'uses' => 'API\UserController@change_profil',
            'middleware' => ['auth:api']
        ]);

        Route::get('sms_otp/androido', [
        'uses' => 'API\UserController@smsOtpO',
            'middleware' => ['auth:api']
        ]);

        Route::post('lacak', [
            'uses' => 'API\UserController@tracking',
            'middleware' => ['auth:api']
        ]);

        Route::post('lacak-masyarakat', [
            'uses' => 'API\UserController@trackingMasyarakat',
            'middleware' => ['auth:api']
        ]);

        Route::get('dinas', [
            'uses' => 'API\DinasController@getDinas',
            'middleware' => ['auth:api']
        ]);

        Route::post('dinas', [
            'uses' => 'API\PersonilController@updateStatusDinas',
            'middleware' => ['auth:api']
        ]);

        Route::get('htchannels', [
            'uses' => 'API\HTController@getAllChannels',
            'middleware' => ['auth:api']
        ]);

        Route::get('channels', [
        'uses' => 'API\HTController@getByKesatuan',
        'middleware' => ['auth:api']
        ]);

        Route::get('logout', [
            'uses' => 'API\UserController@logout',
            'middleware' => ['auth:api']
        ]);

        Route::group([
            'prefix' => 'ht',
            'middleware' => ['auth:api']
        ], function () {
            Route::get('/', [
                'uses' => 'API\HTController@index'
            ]);

            Route::post('/', [
                'uses' => 'API\HTController@store'
            ]);

            Route::post('/{ht}', ['uses' => 'API\HTController@update']);
            Route::delete('/{ht}', ['uses' => 'API\HTController@delete']);
        });
    });
    // END USER ROUTES


    Route::group([
        'prefix' => 'penerangan-satuan',
        'middleware' => ['auth:api']
    ], function (){
        Route::get('/', [
        'uses' => 'API\PeneranganSatuanController@index'
        ]);

        Route::get('{peneranganSatuan}', [
        'uses' => 'API\PeneranganSatuanController@get'
        ]);

        Route::delete('{peneranganSatuan}', [
            'uses' => 'API\PeneranganSatuanController@delete'
        ]);

        Route::post('/', [
            'uses' => 'API\PeneranganSatuanController@tambah'
        ]);

        Route::post('upload', [
        'uses' => 'API\PeneranganSatuanController@upload'
        ]);
    });

    Route::group([
        'prefix' => 'informasi',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('/', [
            'uses' => 'API\InformasiController@getAll'
        ]);

        Route::post('/', [
            'uses' => 'API\InformasiController@tambah'
        ]);

        Route::get('aktif', [
            'uses' => 'API\InformasiController@getAktif'
        ]);

        Route::post('{informasi}', [
            'uses' => 'API\InformasiController@ubahInformasi'
        ]);
    });

    Route::group([
        'prefix' => 'bhabin',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('desa', [
            'uses' => 'API\BhabinController@getDesa'
        ]);
    });

    Route::group([
        'prefix' => 'sispammako',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('/', [
            'uses' => 'API\SispammakoController@getAll'
        ]);

        Route::post('/', [
            'uses' => 'API\SispammakoController@tambah'
        ]);

        Route::post('trigger-alarm', [
            'uses' => 'API\SispammakoController@triggerAlarm'
        ]);

        Route::get('{sispammako}', [
            'uses' => 'API\SispammakoController@get'
        ]);
    });

    Route::group([
        'prefix' => 'email',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('/', [
            'uses' => 'API\EmailController@index'
        ]);

        Route::post('/', [
            'uses' => 'API\EmailController@tambah'
        ]);

        Route::get('{email}', [
            'uses' => 'API\EmailController@get'
        ]);
    });

    // Kesatuan
    Route::group([
        'prefix' => 'kesatuan',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('/', [
            'uses' => 'API\KesatuanController@ambilSemua'
        ]);

        Route::get('/all', [
            'uses' => 'API\KesatuanController@index'
        ]);

        Route::post('/', [
            'uses' => 'API\KesatuanController@store'
        ]);

        Route::post('/{kesatuan}', ['uses' => 'API\KesatuanController@update']);
        Route::delete('/{kesatuan}', ['uses' => 'API\KesatuanController@delete']);
    });
    // endof kesatuan

    // Jabatan
    Route::group([
        'prefix' => 'jabatan',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('/', [
            'uses' => 'API\JabatanController@ambilSemua'
        ]);
    });
    // endof kesatuan

    // Pangkat
    Route::group([
        'prefix' => 'pangkat',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('/', [
            'uses' => 'API\PangkatController@ambilSemua'
        ]);
    });
    // endof kesatuan

    // KEJADIAN GROUP

    Route::group([
        'prefix' => 'kejadian',
        'middleware' => ['auth:api']
    ], function () {

        Route::get('/', [
            'uses' => 'API\KejadianController@listkejadian',
        ]);
        Route::post('/', [
            'uses' => 'API\KejadianController@create_kejadian'
        ]);

        Route::get('detail', [
            'uses' => 'API\KejadianController@getDetail'
        ]);

        Route::get('/total', [
            'uses' => 'API\KejadianController@total'
        ]);

        Route::get('{kejadian}', [
            'uses' => 'API\KejadianController@detailkejadian'
        ]);

        Route::post('{kejadian}/unfollow', [
            'uses' => 'API\KejadianController@unfollow'
        ]);

        Route::post('{kejadian}/komentar', [
            'uses' => 'API\KejadianController@postkomentar'
        ]);

        Route::post('{kejadian}/tindaklanjut', [
            'uses' => 'API\KejadianController@buatTindakLanjut'
        ]);

        Route::post('/broadcast', [
            'uses' => 'API\KejadianController@broadcast'
        ]);
    });

    Route::group([
        'prefix' => 'pengaduan',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('/', [
            'uses' => 'API\PengaduanController@getAll'
        ]);

        Route::post('/', [
            'uses' => 'API\PengaduanController@tambah'
        ]);

        Route::get('{pengaduan}', [
            'uses' => 'API\PengaduanController@lihat'
        ]);

        Route::get('{pengaduan}/komentar', [
            'uses' => 'API\PengaduanController@lihatKomentar'
        ]);

        Route::post('{pengaduan}/komentar', [
            'uses' => 'API\PengaduanController@buatKomentar'
        ]);
    });

    Route::group([
        'prefix' => 'admin',
        'middleware' => ['auth:api']
    ], function (){
        Route::get('/', [
            'uses' => 'API\AdminController@getVisible'
        ]);
    });

    //Call
    Route::group([
        'prefix' => 'call',
        'middleware' => ['auth:api']
    ], function () {
        Route::post('request-to-admin', [
            'uses' => 'API\CallController@createCallFromPersonil'
        ]);

        Route::post('request', [
            'uses' => 'API\CallController@createCall'
        ]);

        Route::get('ready', [
            'uses' => 'API\CallController@ready'
        ]);

        Route::post('update', [
            'uses' => 'API\CallController@updateCall'
        ]);

        Route::get('history', [
            'uses' => 'API\CallController@getCall'
        ]);
    });

    // END KEJADIAN GROUP

    // Darurat
    Route::group([
        'prefix' => 'darurat',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('/', [
            'uses' => 'API\DaruratController@lihatSemua'
        ]);

        Route::get('{darurat}', [
            'uses' => 'API\DaruratController@lihat'
        ]);

        Route::post('{darurat}/selesai', [
        'uses' => 'API\DaruratController@selesai'
        ]);

        Route::post('/', [
            'uses' => 'API\DaruratController@tambah'
        ]);

        Route::post('kejadian-darurat', [
        'uses' => 'API\KejadianController@kejadian_darurat'
        ]);
    });
    // End Darurat

    Route::group([
        'prefix' => 'tigapilar',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('/', [
            'uses' => 'API\TigaPilarController@getAll'
        ]);

        Route::post('/', [
        'uses' => 'API\TigaPilarController@tambah'
        ]);

        Route::post('{tiga_pilar}', [
            'uses' => 'API\TigaPilarController@update'
        ]);

        Route::delete('{tiga_pilar}', [
        'uses' => 'API\TigaPilarController@delete'
        ]);
    });

    // Berita
    Route::group([
        'prefix' => 'news',
        //'middleware' => ['auth:api']
    ], function () {
        Route::get('/', [
            'uses' => 'API\NewsController@semua',
            'middleware' => ['auth:api']
        ]);

        Route::get('/slide', [
            'uses' => 'API\NewsController@slide',
            'middleware' => ['auth:api']
        ]);

        Route::get('{news}', [
            'uses' => 'API\NewsController@get',
            'middleware' => ['auth:api']
        ]);

        Route::post('/', ['uses' => 'API\NewsController@tambah']);

    });

    // KEGIATAN
    Route::group([
        'prefix' => 'kegiatan',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('/', [
            'uses' => 'API\KegiatanController@list_laporan'
        ]);

        Route::post('/', [
            'uses' => 'API\KegiatanController@upload_laporan'
        ]);

        Route::get('tipejenis', [
            'uses' => 'API\KegiatanController@getJenisTipe'
        ]);

        Route::get('{kegiatan}', [
            'uses' => 'API\KegiatanController@detail_laporan'
        ]);

        Route::get('{kegiatan}/komentar', [
            'uses' => 'API\KegiatanController@getKomentar'
        ]);

        Route::post('{kegiatan}/komentar', [
            'uses' => 'API\KegiatanController@tambahKomentar'
        ]);

    });

    // End kegiatan

    // KEGIATAN Bhabin
    Route::group([
        'prefix' => 'kegiatan-bhabin',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('/', [
            'uses' => 'API\KegiatanBhabinController@index'
        ]);

        Route::post('/', [
            'uses' => 'API\KegiatanBhabinController@create'
        ]);

        Route::get('indikator-jenis-kategori-bidang', [
            'uses' => 'API\KegiatanBhabinController@getIndikatorJenisKategoriBidang'
        ]);
        
        Route::get('search-masyarakat', [
            'uses' => 'API\KegiatanBhabinController@searchNikMasyarakat'
        ]);

        Route::get('{kegiatan}', [
            'uses' => 'API\KegiatanBhabinController@detail'
        ]);

        Route::get('{kegiatan}/komentar', [
            'uses' => 'API\KegiatanBhabinController@getKomentar'
        ]);

        Route::post('{kegiatan}/komentar', [
            'uses' => 'API\KegiatanBhabinController@tambahKomentar'
        ]);

        Route::post('create-masyarakat', [
            'uses' => 'API\KegiatanBhabinController@createMasyarakat'
        ]);
    });

    // Laporan Kegiatan Bhabin
    Route::group([
        'prefix' => 'laporan-kegiatan-bhabin',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('/laporan', [
            'uses' => 'API\LaporanKegiatanBhabinController@laporan'
        ]);
        Route::post('/export/{id}', [
            'uses' => 'API\LaporanKegiatanBhabinController@export'
        ]);
    });

    // End laporan kegiatan bhabin

    // pengaturan
    Route::group([
        'prefix' => 'pengaturan',
        'middleware' => ['auth:api']
    ], function () {
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
    });
    // pengaturan

    // PERSONIL
    Route::group([
        'prefix' => 'personil',
        'middleware' => ['auth:api'],
    ], function () {
        Route::get('tracking', [
            'uses' => 'API\PersonilController@tracking'
        ]);
        Route::get('pengawalan', [
            'uses' => 'API\PersonilController@pengawalan'
        ]);
        Route::get('patroli', [
            'uses' => 'API\PersonilController@patroli'
        ]);

        Route::get('/', [
            'uses' => 'API\PersonilController@index'
        ]);

        Route::post('/', [
            'uses' => 'API\PersonilController@tambah'
        ]);

        Route::get('{personil}/ptt', [
            'uses' => 'API\PersonilController@ubahPttHt'
        ]);

        Route::delete('/{personil}', ['uses' => 'API\PersonilController@delete']);

        Route::post('foto', [
            'uses' => 'API\PersonilController@uploadFoto'
        ]);

        Route::get('{personil}', [
            'uses' => 'API\PersonilController@lihat'
        ]);

        Route::post('{personil}/edit', [
            'uses' => 'API\PersonilController@edit'
        ]);

        Route::get('lihat/tracking', [
            'uses' => 'API\PersonilController@tracking'
        ]);
        
        Route::post('reset_password', [
            'uses' => 'API\PersonilController@resetPassword'
        ]);
    });

    Route::group([
        'prefix' => 'disposisi',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('/', 'API\DisposisiController@index');
        Route::get('/jenis', 'API\DisposisiController@jenis');
        Route::post('/', 'API\DisposisiController@buat');
        Route::post('/upload', 'API\DisposisiController@upload');
        Route::get('{surat}', 'API\DisposisiController@detail');
        Route::delete('{surat}', 'API\DisposisiController@hapus');
        Route::post('{surat}/edit', 'API\DisposisiController@ubahSurat');
        Route::post('{surat}/disposisi', 'API\DisposisiController@tambahDisposisi');
        Route::delete('disposisi/{disposisi}', 'API\DisposisiController@hapusDisposisi');
    });

    // Masyarakat
    Route::group([
        'prefix' => 'masyarakat',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('/', ['uses' => 'API\MasyarakatController@index']);
        Route::post('/{kesatuan}', ['uses' => 'API\MasyarakatController@update']);
        Route::delete('/{kesatuan}', ['uses' => 'API\MasyarakatController@delete']);
    });
    // endof masyarakat

    // JENIS
    Route::group([
        'prefix' => 'jenis',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('/', [
            'uses' => 'API\JenisController@index'
        ]);
    });
    //JENIS END

    // TEMPAT VITAL
    Route::group([
        'prefix' => 'tempat-vital',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('jenis/{id}', [
            'uses' => 'API\TempatVitalController@getByJenis'
        ]);
        Route::post('all', [
            'uses' => 'API\TempatVitalController@getAll'
        ]);
    });
    //TEMPAT VITAL END

    // Form Laporan
    Route::group([
        'prefix' => 'form',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('tipe-laporan', 'API\ExportLaporan@selectTipe');
        Route::get('jenis-giat', 'API\ExportLaporan@jenisGiat');
    });
    //end Form

    Route::group([
        'prefix' => 'export-laporan',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('show-data/{id}', 'API\ExportLaporan@index');
        Route::post('show-data/{id}', 'API\ExportLaporan@index');
        Route::POST('download/{option}', 'API\ExportLaporan@exportExcelbyChecklist');
    });

    Route::group([
        'prefix' => 'dashboard',
        'middleware' => ['auth:api']
    ], function () {
        Route::get('/', 'API\DashboardController@index');
        Route::get('/personil-kegiatan', 'API\DashboardController@ambilDataPersonilPengirimKegiatan');
    });

    Route::group([
        'prefix' => 'check-updates'
    ], function (){
        Route::get('internal', function (){
            $latestVersion = (\App\Pengaturan::where('nama', 'latest_version_internal')->first())->nilai;
            $latestVersionCode = (\App\Pengaturan::where('nama', 'latest_version_code_internal')->first())->nilai;
            $url = (\App\Pengaturan::where('nama', 'url_internal')->first())->nilai;
            $releaseNote = json_decode((\App\Pengaturan::where('nama', 'release_note_internal')->first())->nilai);

            return response()->json(compact('latestVersion', 'latestVersionCode', 'url', 'releaseNote'));
        });

        Route::get('external', function (){
            $latestVersion = (\App\Pengaturan::where('nama', 'latest_version_external')->first())->nilai;
            $latestVersionCode = (\App\Pengaturan::where('nama', 'latest_version_code_external')->first())->nilai;
            $url = (\App\Pengaturan::where('nama', 'url_external')->first())->nilai;
            $releaseNote = json_decode((\App\Pengaturan::where('nama', 'release_note_external')->first())->nilai);

            return response()->json(compact('latestVersion', 'latestVersionCode', 'url', 'releaseNote'));
        });
    });

    Route::group([
        'prefix' => 'dansos',
        'middleware' => ['auth:api']
    ], function (){
        Route::group([
            'prefix' => 'laporan'
        ], function (){
        Route::get('/', 'API\DansosController@getLaporan');
        Route::post('/', 'API\DansosController@tambahLaporan');
        Route::delete('{dansos_laporan}', 'API\DansosController@deleteLaporan');
        Route::post('{dansos_laporan}', 'API\DansosController@ubahLaporan');
        });

        Route::group([
            'prefix' => 'pagu'
        ], function (){
            Route::get('/', 'API\DansosController@getPagu');
            Route::post('/', 'API\DansosController@tambahPagu');
            Route::delete('{dansos_pagu}', 'API\DansosController@deletePagu');
            Route::post('{dansos_pagu}', 'API\DansosController@ubahPagu');
        });
    });

    Route::group([
        'prefix' => 'danadesa',
        'middleware' => ['auth:api']
    ], function () {
        
        Route::group([
            'prefix' => 'profil'
        ], function (){
            Route::post('/', 'API\ProfilKelurahanController@updateCreate');
            Route::get('{kelurahan}', 'API\ProfilKelurahanController@profile');
        });

        Route::group([
            'prefix' => 'belanja',
        ], function (){
            Route::post('/', 'API\ProfilKelurahanController@createBelanjaBidang');
            Route::get('fetch-kelurahan/{kelurahan}', 'API\ProfilKelurahanController@belanjaBidang');
            Route::get('{belanja}', 'API\ProfilKelurahanController@detailBelanjaBidang');
            Route::delete('{belanja}', 'API\ProfilKelurahanController@deleteBelanjaBidang');
            Route::patch('{belanja}', 'API\ProfilKelurahanController@updateBelanjaBidang');
        });

        Route::group([
            'prefix' => 'pendapatan'
        ], function (){
            Route::get('fetch-kelurahan/{kelurahan}', 'API\ProfilKelurahanController@pendapatan');
            Route::post('/', 'API\ProfilKelurahanController@createPendapatan');
            Route::patch('{pendapatan}', 'API\ProfilKelurahanController@updatePendapatan');
            Route::get('{pendapatan}', 'API\ProfilKelurahanController@detailPendapatan');
            Route::delete('{pendapatan}', 'API\ProfilKelurahanController@deletePendapatan');
        });

        Route::group([
            'prefix' => 'rincianbelanja'
        ], function(){
            Route::post('/', 'API\ProfilKelurahanController@createRincianBelanja');
            Route::get('fetch-kelurahan/{kelurahan}', 'API\ProfilKelurahanController@RincianBelanja');
            Route::get('{rincianbelanja}', 'API\ProfilKelurahanController@detailRincianBelanja');
            Route::patch('{rincian_belanja}', 'API\ProfilKelurahanController@updateRincianBelanja');
            Route::delete('{rincian}', 'API\ProfilKelurahanController@deleteRincianBelanja');
        });

        Route::group([
        'prefix' => 'giatdanadesa'
        ], function (){
            Route::post('/', 'API\ProfilKelurahanController@createGiatDanaDesa');
            Route::get('fetch-kelurahan/{kelurahan}', 'API\ProfilKelurahanController@GiatDanaDesa');
            Route::get('{dana_desa}', 'API\ProfilKelurahanController@detailGiatDanaDesa');
            Route::delete('{dana_desa}', 'API\ProfilKelurahanController@deleteGiatDanaDesa');
            Route::post('{dana_desa}', 'API\ProfilKelurahanController@updateGiatDanaDesa');
        });

        Route::get('/desa/all', 'API\ProfilKelurahanController@getDesa');

    });

    Route::get('provinsi', function () {
        $provinsi = \App\Provinsi::with(['kabupaten'])->get();
        return response()->json(['provinsi' => $provinsi]);
    });

    Route::get('kabupaten-provinsi', function () {
        $where = env('TINGKAT_DANA_DESA', 'provinsi') == 'kabupaten' ? 'id_kab' : 'id_prov';
        $kabupaten = \App\Kabupaten::where($where, env('PROVINSI_DANA_DESA'))->get();
        return response()->json(['kabupaten' => $kabupaten]);
    });

    Route::get('kecamatan/{kabupaten}', function ($id) {
        $kecamatan = \App\Kecamatan::where('id_kab', $id)->get();
        return response()->json(['kecamatan' => $kecamatan]);
    });

    Route::get('kelurahan/{kecamatan}', function ($id) {
        $kelurahan = \App\Kelurahan::where('id_kec', $id)->get();
        return response()->json(['kelurahan' => $kelurahan]);
    });

    Route::get('wilayah', function () {
        $id_kab = explode(',', env('APP_KAB'));
        $kecamatan = App\Kecamatan::whereIn('id_kab', $id_kab)->orderBy('nama', 'asc')->get();

        return fractal()
            ->collection($kecamatan)
            ->parseIncludes('kelurahan')
            ->transformWith(App\Transformers\KecamatanTransformer::class)
            ->serializeWith(App\Serializers\DataArraySansIncludeSerializer::class)
            ->respond();
    });

    Route::get('wilayahkab', function () {
        $id_kab = explode(',', env('APP_KAB'));
        $kecamatan = App\Kabupaten::whereIn('id_kab', $id_kab)->orderBy('nama', 'asc')->get();

        return fractal()
            ->collection($kecamatan)
            ->parseIncludes('kecamatan.kelurahan')
            ->transformWith(App\Transformers\KabupatenTransformer::class)
            ->serializeWith(App\Serializers\DataArraySansIncludeSerializer::class)
            ->respond();
    });

    Route::get('get-log-personil', [
        'uses' => 'API\LogPersonilController@index',
        'middleware' => ['auth:api']
    ]);

    Route::group([
        'prefix' => 'absensi',
        'middleware' => ['auth:api']
    ], function () {
        Route::post('/', 'API\AbsensiController@index');
        Route::post('/export', 'API\AbsensiController@export');
    });

    if (env('APP_ENV') !== 'production') {
        Route::post('default_banner', function (Request $request) {
            $banner = $request->banner->storeAs(
                'banner_grid',
                str_random(40) . '.' . $request->banner->extension()
            );

            $pengaturan = App\Pengaturan::getByKey('default_banner_grid')->first();
            $pengaturan->nilai = $banner;

            if ($pengaturan->save())
                return response()->json(['berhasil']);

            return response()->json(['error']);
        });
    }

    Route::group([
        'prefix' => 'buku-saku',
        'middleware' => ['auth:api']
    ], function (){
        Route::get('/', [
            'uses' => 'API\BukuSakuController@getAll'
        ]);

        Route::post('/', [
            'uses' => 'API\BukuSakuController@tambah'
        ]);

        Route::get('{bukusaku}', [
            'uses' => 'API\BukuSakuController@get'
        ]);
    });

    // Mastumapel
    Route::group([
    'prefix' => 'tps',
    'middleware' => ['auth:api']
    ], function (){
        Route::get('/', [
            'uses' => 'API\TpsController@getAll'
        ]);

        Route::get('/monit', [
            'uses' => 'API\TpsController@tpsMonit'
        ]);

        Route::get('/jumlah', [
            'uses' => 'API\TpsController@jumlahTps'
        ]);

        Route::get('{tps}', [
        'uses' => 'API\TpsController@get'
        ]);

        Route::post('{tps}', [
        'uses' => 'API\TpsController@perolehan'
        ]);

        Route::post('{tps}/lokasi', [
            'uses' => 'API\TpsController@updateLokasi'
        ]);
    });
    // End of mastumapel

    Route::group([
        'prefix' => 'pray',
        'middleware' => ['auth:api']
    ], function (){
        Route::get('today', 'API\PrayerTimeController@getToday');
    });

    //Start report dana desa
    Route::group([
    'prefix' => 'report',
    'middleware' => ['auth:api']
    ], function (){
        Route::get('/pendapatan', 'API\DanaReportController@pendapatan');
        Route::get('/belanja', 'API\DanaReportController@belanja');
        Route::get('/rincian', 'API\DanaReportController@rincian');
        Route::get('/dansos', 'API\DanaReportController@dansos');
        Route::get('/pendapatan-dansos', 'API\DanaReportController@pendapatanDansos');
        Route::post('/export-dana-desa', 'API\DanaReportController@exportDanaDesa');
        Route::post('/export-dana-sosial', 'API\DanaReportController@exportDanaSosial');
    });
    // End of report dana desa
    
    //Start titik api
    Route::group([
        'prefix' => 'titik-api',
        'middleware' => ['auth:api']
    ], function (){
        Route::get('/', 'API\TitikApiController@index');
        Route::get('/detail/{hotspot}', 'API\TitikApiController@detail');
    });
    // End of titik api

    //SP2HP
    Route::group([
        'prefix' => 'sp2hp',
    ], function (){
        Route::get('search', [
            'uses' => 'API\Sp2hpController@search',
            'as' => 'api.sp2hp.search',
            'middleware' => ['auth:api'],
        ]);
        Route::post('upload', [
        'uses' => 'API\Sp2hpController@upload'
        ]);
    });
    Route::post('sms-callback', [
        'uses' => 'API\Sp2hpController@smsCallback'
    ]);
    //End SP2HP

    Route::get('upload/{pathA}/{pathB}/{pathC?}', function ($pathA, $pathB, $pathC = null) {
        $path = "{$pathA}/{$pathB}";
        if ($pathC !== null) $path .= "/{$pathC}";
        $mime = Storage::mimeType($path);
        $allowedMime = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'image/svg+xml'];

        if (!in_array($mime, $allowedMime))
            return response()->json(['error' => 'Tidak terpenuhi.'], 400);
        else
            return response()->file(storage_path("app/{$path}"));
    });
});
