<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonilTerdekatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personil_terdekat', function (Blueprint $table) {
            $table->id();
            $table->integer('id_personil')->unsigned();
            $table->integer('id_induk')->unsigned();
            $table->string('jenis_induk');
            $table->double('lat')->default(0.0);
            $table->double('lng')->default(0.0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personil_terdekat');
    }
}
