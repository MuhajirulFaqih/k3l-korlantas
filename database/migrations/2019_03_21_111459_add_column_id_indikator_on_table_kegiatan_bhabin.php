<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnIdIndikatorOnTableKegiatanBhabin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kegiatan_bhabhin', function (Blueprint $table) {
            $table->integer('id_indikator')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kegiatan_bhabhin', function (Blueprint $table) {
            $table->dropColumn('id_indikator');
        });
    }
}
