<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilKelurahanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profil_kelurahan', function (Blueprint $table) {
            $table->increments('id');
            $table->char('id_kel', 10);
            $table->string('kades');
            $table->string('sekdes');
            $table->bigInteger('luas_daerah');
            $table->enum('satuan_luas', ['km²', 'ha', 'm²'])->default('km²');
            $table->char('jumlah_penduduk', 15);
            $table->string('batas_utara');
            $table->string('batas_selatan');
            $table->string('batas_timur');
            $table->string('batas_barat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profil_kelurahan');
    }
}
