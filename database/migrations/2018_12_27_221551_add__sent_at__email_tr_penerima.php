<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSentAtEmailTrPenerima extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_tr_penerima', function (Blueprint $table) {
            $table->timestamp('sent_at')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_tr_penerima', function (Blueprint $table) {
            $table->dropColumn('sent_at');
        });
    }
}
