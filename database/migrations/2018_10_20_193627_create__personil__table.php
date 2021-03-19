<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personil', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nrp');
            $table->string('nama');
            $table->integer('id_pangkat')->unsigned();
            $table->integer('id_jabatan')->unsigned();
            $table->integer('id_kesatuan')->unsigned();
            $table->string('kelamin')->nullable();
            $table->string('status_dinas')->nullable();
            $table->string('bearing')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personil');
    }
}
