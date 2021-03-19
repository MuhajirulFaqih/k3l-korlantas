<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaslonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paslon', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_ka');
            $table->string('nama_waka');
            $table->string('foto');
            $table->integer('no_urut')->nullable();
            $table->string('color')->default('#0000');
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
        Schema::dropIfExists('paslon');
    }
}
