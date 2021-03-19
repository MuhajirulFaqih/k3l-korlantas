<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasyarakatKegiatanBhabinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('masyarakat_kegiatan_bhabin', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nik', 20)->nullable();
            $table->string('nama')->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('suku', 100)->nullable();
            $table->integer('id_agama')->nullable();
            $table->mediumText('alamat')->nullable();
            $table->string('pekerjaan', 200)->nullable();
            $table->string('no_hp', 12)->nullable();
            $table->string('status_keluarga')->nullable();
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
        Schema::dropIfExists('masyarakat_kegiatan_bhabin');
    }
}
