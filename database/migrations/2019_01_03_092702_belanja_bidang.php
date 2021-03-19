<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BelanjaBidang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('belanja_bidang', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_kel');
            $table->bigInteger('penyelenggaraan');
            $table->bigInteger('pelaksanaan');
            $table->bigInteger('pemberdayaan');
            $table->bigInteger('pembinaan');
            $table->bigInteger('tak_terduga');
            $table->year('tahun_anggaran');
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
        Schema::dropIfExists('belanja_bidang');
    }
}
