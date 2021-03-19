<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWaktuDinasOnTableLogPersonil extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_status_personil', function (Blueprint $table) {
            $table->timestamp('waktu_mulai_dinas')->after('status_dinas')->nullable();
            $table->timestamp('waktu_selesai_dinas')->after('waktu_mulai_dinas')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('log_status_personil', function (Blueprint $table) {
            $table->dropColumn('waktu_mulai_dinas', 'waktu_selesai_dinas');
        });
    }
}
