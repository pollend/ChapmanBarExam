<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShortAnswerResponseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('short_answer_response', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('content');

            $table->bigInteger('short_answer_question_id')->unsigned();
            $table->foreign('short_answer_question_id')
                ->references('id')
                ->on('short_answer_question')
                ->onDelete('cascade');

            $table->bigInteger('session_id')->unsigned();
            $table->foreign('session_id')
                ->references('id')
                ->on('quiz_session')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('short_answer_response');
    }
}
