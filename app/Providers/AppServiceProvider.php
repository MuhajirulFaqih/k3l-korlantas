<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Darurat;
use App\Models\Kegiatan;
use App\Models\Kejadian;
use App\Models\Kesatuan;
use App\Models\Masyarakat;
use App\Models\Pengaduan;
use App\Models\Personil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        setlocale(LC_TIME, 'id_ID.UTF-8');
        config(['app.locale' => 'id']);
	    \Carbon\Carbon::setLocale('id');

        Schema::defaultStringLength(191);

        Relation::morphMap([
            'admin' => Admin::class,
            'personil' => Personil::class,
            'masyarakat' => Masyarakat::class,
            'kegiatan' => Kegiatan::class,
            'kejadian' => Kejadian::class,
            'pengaduan' => Pengaduan::class,
            'darurat' => Darurat::class,
            'kesatuan' => Kesatuan::class
        ]);

        if (Schema::hasTable('pengaturan')) {
            foreach (DB::table('pengaturan')->where('autoload', true)->get() as $pengaturan) {
                \Config::set("pengaturan.{$pengaturan->nama}", $pengaturan->nilai);
            }
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        require_once __DIR__ . '/../Helpers/filter_tgl.php';
    }
}
