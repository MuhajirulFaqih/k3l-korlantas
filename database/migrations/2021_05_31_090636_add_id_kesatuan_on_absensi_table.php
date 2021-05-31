<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdKesatuanOnAbsensiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('absensi_personil', function (Blueprint $table) {
            $table->integer('id_kesatuan')->after('id_personil')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('absensi_personil', function (Blueprint $table) {
            $table->dropColumn('id_kesatuan');
        });
    }
}
