<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdKesatuanOnKejadianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::table('kejadian', function (Blueprint $table) {
            $table->integer('id_kesatuan')->after('id_user')->nullable();
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
            $table->dropColumn('id_kesatuan');
        });
    }
}
