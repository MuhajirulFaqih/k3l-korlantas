<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAksesDanaDesaPersJabatan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pers_jabatan', function (Blueprint $table) {
            $table->boolean('aksess_dana_desa')->default(false)->after('status_pimpinan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pers_jabatan', function (Blueprint $table) {
            $table->dropColumn('aksess_dana_desa');
        });
    }
}
