<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeneranganSatuanFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penerangan_satuan_file', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_penerangan_satuan')->index()->unsigned();
            $table->string('file');
            $table->string('thumbnails')->nullable();
            $table->string("file_name")->nullable();
            $table->string('type')->default('image');
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
        Schema::dropIfExists('penerangan_satuan_file');
    }
}
