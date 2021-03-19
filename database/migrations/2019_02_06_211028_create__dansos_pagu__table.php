<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDansosPaguTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagu_dansos', function (Blueprint $table) {
            $table->increments('id');
            $table->char('id_kel', 15);
            $table->string("pagu");
            $table->string('asal');
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
        Schema::dropIfExists('pagu_dansos');
    }
}
