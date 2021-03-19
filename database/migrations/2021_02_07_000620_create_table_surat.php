<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSurat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat', function (Blueprint $table) {
            $table->increments('id');
            $table->date('tanggal')->nullable();
            $table->string('id_asal');
            $table->dateTime('waktu_diterima')->nullable();
            $table->string('nomor')->nullable();
            $table->string('no_agenda')->nullable();
            $table->string('pengirim')->nullable();
            $table->text('perihal')->nullable();
            $table->string('derajat')->default('BIASA');
            $table->string('klasifikasi')->default('biasa');
            $table->string('file');
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
        Schema::dropIfExists('surat');
    }
}
