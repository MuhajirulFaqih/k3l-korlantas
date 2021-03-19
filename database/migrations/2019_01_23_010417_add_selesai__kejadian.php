<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSelesaiKejadian extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kejadian', function (Blueprint $table) {
            $table->boolean('follow_me')->default(false);
            $table->boolean('selesai')->default(false);
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
            $table->dropColumn('selesai', 'follow_me');
        });
    }
}
