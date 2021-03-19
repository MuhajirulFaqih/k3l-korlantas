<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnPositionAbsensiPersonilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('absensi_personil', function (Blueprint $table) {
            $table->double('lat_datang')->after('waktu_selesai')->nullable();
            $table->double('lng_datang')->after('lat_datang')->nullable();
            $table->double('lat_pulang')->after('lng_datang')->nullable();
            $table->double('lng_pulang')->after('lat_pulang')->nullable();
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
            $table->dropColumn('lat_datang', 'lng_datang', 'lat_pulang', 'lng_pulang');
        });
    }
}
