<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class JenisKegiatan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kegiatan_jenis',function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('jenis');
            $table->string('jenis_singkat')->nullable()->default(null);
            $table->string('status')->nullable();
            $table->timestamp('w_tambah')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('w_ubah')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kegiatan_jenis');
    }
}
