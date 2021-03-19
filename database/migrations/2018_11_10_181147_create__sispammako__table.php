<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSispammakoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sispammako', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_user')->index()->nullable();
            $table->enum('jenis', ['alarmstelling', 'sispamkota', 'plb']);
            $table->text('arahan');
            $table->string('dokumen');
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
        Schema::dropIfExists('sispammako');
    }
}
