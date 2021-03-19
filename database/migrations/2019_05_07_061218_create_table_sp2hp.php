<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSp2hp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sp2hp', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no_surat', 150)->nullable();
            $table->string('no_telp', 50)->nullable();
            $table->string('pelapor')->nullable();
            $table->string('kasus')->nullable();
            $table->string('link')->nullable();
            $table->string('kode_unik')->nullable();
            $table->string('id_sms')->nullable();
            $table->string('status')->nullable();
            $table->text('sms')->nullable();
            $table->date('tgl_surat')->nullable();
            $table->string('kesatuan')->default('Reskrim')->nullable();
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
        Schema::dropIfExists('sp2hp');
    }
}
