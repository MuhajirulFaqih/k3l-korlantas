<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdJenisKesatuanOnPersKesatuanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pers_kesatuan', function (Blueprint $table) {
            $table->integer('id_jenis_kesatuan')->after('kode_satuan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pers_kesatuan', function (Blueprint $table) {
            $table->dropColumn('id_jenis_kesatuan');
        });
    }
}
