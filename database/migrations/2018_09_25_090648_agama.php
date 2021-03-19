<?php

/**
 * @Author: Alan
 * @Date:   2018-09-25 16:06:48
 * @Last Modified by:   Alan
 * @Last Modified time: 2018-09-25 16:09:05
 */
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Agama extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agama',function(Blueprint $table){
            $table->increments('id');
            $table->string('agama');
            $table->string('status');
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
        Schema::dropIfExists('agama');
    }
}
