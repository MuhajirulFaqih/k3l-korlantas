<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
// use function foo\func;

class LapKegiatan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kegiatan',function (Blueprint $table){
            $table->increments('id');
            $table->string('id_user')->nullable();
            $table->integer('tipe_laporan')->unsigned()->nullable();
            $table->integer('jenis_kegiatan')->unsigned()->nullable();
            $table->dateTime('waktu_kegiatan');
            $table->string('judul');
            $table->string('kuat_pers')->nullable();
            $table->mediumText('hasil')->nullable();
            $table->string('jml_giat')->nullable();
            $table->string('jml_tsk')->nullable();
            $table->mediumText('bb')->nullable();
            $table->mediumText('perkembangan')->nullable();
            $table->mediumText('dasar')->nullable();
            $table->mediumText('keterangan')->nullable();
            $table->string('dokumentasi')->nullable();
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
        Schema::dropIfExists('kegiatan');
    }
}
