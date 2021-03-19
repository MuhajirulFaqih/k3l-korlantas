<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLatLngKegiatan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->double('lat')->after('tipe_laporan');
            $table->double('lng')->after('lat');
            $table->string('sasaran')->after('judul')->nullable();
            $table->string('lokasi')->after('sasaran')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->dropColumn('lat', 'lng', 'sasaran', 'lokasi');
        });
    }
}
