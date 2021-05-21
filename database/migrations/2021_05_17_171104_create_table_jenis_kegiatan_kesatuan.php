<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableJenisKegiatanKesatuan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jenis_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->string('jenis')->nullable();
            $table->string('jenis_singkat')->nullable();
            $table->string('keterangan', 30)->nullable();
            $table->boolean('is_quick_response')->default(false);
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
        Schema::dropIfExists('jenis_kegiatan');
    }
}
