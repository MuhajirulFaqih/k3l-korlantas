<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProfilKelUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profil_kelurahan', function (Blueprint $table) {
            $table->string('lat');
            $table->string('long');
            $table->string('zoom');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profil_kelurahan', function (Blueprint $table) {
            $table->double('lat');
            $table->double('long');
            $table->double('zoom');
        });
    }
}
