<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersKesatuanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pers_kesatuan', function (Blueprint $table) {
            $table->id();
            $table->integer('id_jenis')->nullable();
            $table->string('kesatuan');
            $table->char('kode_satuan', 15)->nullable();
            $table->integer('level')->default(1);
            $table->string('icon')->nullable();
            $table->string('banner_grid')->nullable();
            $table->nestedSet();
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
        Schema::dropIfExists('pers_kesatuan');
    }
}
