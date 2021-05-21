<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogStatusPersonil extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_status_personil', function (Blueprint $table) {
            $table->id();
            $table->integer('id_personil');
            $table->integer('status_dinas');
            $table->timestamp('waktu_mulai_dinas')->nullable();
            $table->timestamp('waktu_selesai_dinas')->nullable();
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
        Schema::dropIfExists('log_status_personil');
    }
}
