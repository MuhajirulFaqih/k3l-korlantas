<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKegiatanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user')->unsigned()->index();
            $table->dateTime('waktu_kegiatan');
            $table->text('daftar_rekan')->nullable();
            $table->text('nomor_polisi')->nullable();
            $table->text('detail')->nullable();
            $table->text('rute_patroli')->nullable();
            $table->double('lat');
            $table->double('lng');
            $table->string('dokumentasi');
            $table->string('id_kelurahan_binmas', 50)->nullable();
            $table->boolean('is_quick_response')->default(false);
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
        Schema::dropIfExists('kegiatan');
    }
}
