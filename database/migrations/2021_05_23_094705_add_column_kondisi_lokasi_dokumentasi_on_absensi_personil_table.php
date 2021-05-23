<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnKondisiLokasiDokumentasiOnAbsensiPersonilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('absensi_personil', function (Blueprint $table) {
            $table->string('lokasi_datang', 50)->after('lng_pulang')->nullable();
            $table->string('lokasi_pulang', 50)->after('lokasi_datang')->nullable();
            $table->string('kondisi_datang', 50)->after('lokasi_pulang')->nullable();
            $table->string('kondisi_pulang', 50)->after('kondisi_datang')->nullable();
            $table->string('dokumentasi_datang')->after('kondisi_pulang')->nullable();
            $table->string('dokumentasi_pulang')->after('dokumentasi_datang')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('absensi_personil', function (Blueprint $table) {
            $table->dropColumn(['lokasi_datang', 'lokasi_pulang', 'kondisi_datang', 'kondisi_pulang', 'dokumentasi_datang', 'dokumentasi_pulang']);
        });
    }
}
