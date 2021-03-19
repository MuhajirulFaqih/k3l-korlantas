<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalllogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_call', function (Blueprint $table) {
            $table->increments('id');
            $table->string('from');
            $table->integer('id_from');
            $table->string('to');
            $table->integer('id_to');
            $table->datetime('start')->nullable();
            $table->datetime('end')->nulable();
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
        Schema::dropIfExists('log_call');
    }
}
