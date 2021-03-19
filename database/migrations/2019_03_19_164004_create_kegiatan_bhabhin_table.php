<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKegiatanBhabhinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kegiatan_bhabhin', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('id_jenis')->nullable();
            $table->string('lokasi', 200)->nullable();
            $table->text('uraian')->nullable();

            $table->integer('id_kategori')->nullable();
            $table->text('ringkasan')->nullable();
            $table->text('para_pihak')->nullable();
            $table->text('kronologi')->nullable();
            $table->text('solusi')->nullable();
            $table->integer('id_kecamatan')->nullable();
            
            $table->string('kegiatan')->nullable();
            $table->text('pelaksanaan')->nullable();
            
            $table->integer('id_bidang')->nullable();
            $table->string('sumber_info')->nullable();
            $table->string('nilai_informasi')->nullable();

            $table->dateTime('waktu_kegiatan')->nullable();
            $table->double('lat')->nullable();
            $table->double('lng')->nullable();
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
        Schema::dropIfExists('kegiatan_bhabhin');
    }
}
