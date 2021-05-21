<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKejadianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kejadian', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user');
            $table->integer('id_darurat')->nullable();
            $table->dateTime('w_kejadian');
            $table->string('kejadian');
            $table->string('lokasi');
            $table->text('keterangan');
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
        Schema::dropIfExists('kejadian');
    }
}
