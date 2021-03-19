<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerolehanSuaraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perolehan_suara', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_tps')->unsigned();
            $table->integer('id_paslon')->unsigned();
            $table->integer('suara');
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
        Schema::dropIfExists('perolehan_suara');
    }
}
