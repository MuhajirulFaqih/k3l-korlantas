<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnVerifikasiOnTableKejadian extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kejadian', function (Blueprint $table) {
            $table->integer('verifikasi')->nullable()->after('id_darurat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kejadian', function (Blueprint $table) {
            $table->dropColumn('verifikasi');
        });
    }
}
