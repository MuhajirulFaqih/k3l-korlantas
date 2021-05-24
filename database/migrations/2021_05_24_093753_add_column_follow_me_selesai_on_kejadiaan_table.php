<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnFollowMeSelesaiOnKejadiaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kejadian', function (Blueprint $table) {
            $table->boolean('follow_me')->after('verifikasi')->nullable();
            $table->boolean('selesai')->after('follow_me')->nullable();
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
            $table->dropColumn(['follow_me', 'selesai']);
        });
    }
}
