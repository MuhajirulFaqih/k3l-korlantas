<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnIdUserOnTableKegiatanBhabin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kegiatan_bhabhin', function (Blueprint $table) {
            $table->integer('id_user')->nullable()->after('id_indikator');
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
            $table->dropColumn('id_user');
        });
    }
}
