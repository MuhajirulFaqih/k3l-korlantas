<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallParticipantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call_participant', function (Blueprint $table) {
            $table->id();
            $table->integer('id_call');
            $table->integer('id_user');
            $table->string('connection_id')->nullable();
            $table->string('status')->default('active');
            $table->dateTime('active_at')->nullable();
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
        Schema::dropIfExists('call_participant');
    }
}
