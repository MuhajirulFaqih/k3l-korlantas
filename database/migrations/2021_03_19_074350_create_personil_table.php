<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('nrp');
            $table->string('nama');
            $table->integer('id_pangkat')->unsigned();
            $table->integer('id_jabatan')->unsigned();
            $table->integer('id_kesatuan')->unsigned();
            $table->string('kelamin')->nullable();
            $table->string('alamat')->nullable();
            $table->string('status_dinas')->nullable();
            $table->string('bearing')->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->double('lat')->nullable();
            $table->double('lng')->nullable();
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
