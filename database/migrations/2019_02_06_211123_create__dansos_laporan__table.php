<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDansosLaporanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dansos_laporan', function (Blueprint $table) {
            $table->increments('id');
            $table->char('id_kel', 15);
            $table->string("kepada");
            $table->text("kegunaan");
            $table->bigInteger("jumlah");
            $table->string("foto");
            $table->year("tahun_anggaran");
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
        Schema::dropIfExists('dansos_laporan');
    }
}
