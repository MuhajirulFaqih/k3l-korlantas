<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProviderProviderIdMasyarakat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('masyarakat', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('id_kel');
            $table->string('provider_id')->nullable()->after('provider');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('masyarakat', function (Blueprint $table) {
            $table->dropColumn('provider', 'provider_id');
        });
    }
}
