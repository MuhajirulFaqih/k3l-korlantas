<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdDeskelTigaPilar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tiga_pilar', function (Blueprint $table) {
            $table->char('id_deskel', 15)->after('id')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tiga_pilar', function (Blueprint $table) {
            $table->dropColumn('id_deskel');
        });
    }
}
