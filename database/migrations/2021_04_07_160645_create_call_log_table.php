<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call_log', function (Blueprint $table) {
            $table->id();
            $table->integer('id_admin');
            $table->string('session_id')->nullable();
            $table->string('custom_session_id')->nullable();
            $table->string('recording_id')->nullable();
            $table->string('recording_path')->nullable();
            $table->string('recording_name')->nullable();
            $table->string('recording_resolution')->nullable();
            $table->bigInteger('recording_duration')->nullable();
            $table->boolean('is_calling')->default(true);
            $table->datetime('startTime')->nullable();
            $table->datetime('endTime')->nullable();
            $table->bigInteger('duration')->default(0);
            $table->string('end_reason')->default('lastParticipantLeft');
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
        Schema::dropIfExists('call_log');
    }
}
