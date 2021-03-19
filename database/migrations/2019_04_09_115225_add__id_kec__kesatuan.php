<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdKecKesatuan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pers_kesatuan', function (Blueprint $table) {
            $table->char('id_kec', 15)->after('induk')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pers_kesatuan', function (Blueprint $table) {
            $table->dropColumn('id_kec');
        });
    }
}
