<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDisposisiSurat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat_disposisi', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_surat');
            $table->dateTime('w_diterima')->nullable();
            $table->string('derajat')->default('BIASA');
            $table->string('klasifikasi')->default('BIASA');
            $table->dateTime('w_disposisi');
            $table->string('file');
            $table->text('isi');
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
        Schema::dropIfExists('surat_disposisi');
    }
}
