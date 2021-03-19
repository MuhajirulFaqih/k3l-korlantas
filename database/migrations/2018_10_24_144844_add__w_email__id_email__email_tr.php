<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWEmailIdEmailEmailTr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_tr', function (Blueprint $table) {
            $table->dateTime('w_email')->nullable()->after('pengirim');
            $table->string('id_email')->after('w_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_tr', function (Blueprint $table) {
            $table->dropColumn('w_email', 'id_email');
        });
    }
}
