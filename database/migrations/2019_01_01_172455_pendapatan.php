<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Pendapatan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pendapatan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_kel');
            $table->bigInteger('bagihasilpajakDaerah');
            $table->bigInteger('pendapatanaslidaerah');
            $table->bigInteger('alokasidanaDesa');
            $table->year('tahun_anggaran');
            $table->bigInteger('silpa');
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
        //
    }
}
