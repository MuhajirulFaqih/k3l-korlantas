<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempatVitalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tempat_vital', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_jenis');
            $table->string('nama_tempat');
            $table->string('lokasi');
            $table->double('lat');
            $table->double('lng');
            $table->integer('id_jajaran');
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
        Schema::dropIfExists('tempat_vital');
    }
}
