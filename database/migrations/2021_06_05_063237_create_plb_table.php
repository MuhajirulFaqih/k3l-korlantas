<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plb', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user')->unsigned()->index();
            $table->integer('id_kesatuan')->nullable();
            $table->integer('id_kesatuan_tujuan')->nullable();
            $table->text('keterangan')->nullable();
            $table->double('lat');
            $table->double('lng');
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
        Schema::dropIfExists('plb');
    }
}
