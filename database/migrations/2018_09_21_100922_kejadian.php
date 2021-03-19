<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Kejadian extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kejadian',function (Blueprint $table){
            $table->increments('id');
            $table->string('id_user');
            $table->timestamp('w_kejadian');
            $table->string('kejadian');
            $table->string('lokasi');
            $table->mediumText('keterangan');
            $table->string('lat');
            $table->string('lng');
            $table->string('gambar');
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
        Schema::dropIfExists('kejadian');
    }
}
