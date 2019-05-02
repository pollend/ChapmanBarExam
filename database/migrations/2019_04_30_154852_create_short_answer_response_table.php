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
        Schema::create('quiz_short_answer_responses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('content');

            $table->bigInteger('quiz_short_answer_question_id')->unsigned();
            $table->foreign('quiz_short_answer_question_id')
                ->references('id')
                ->on('quiz_short_answer_questions')
                ->onDelete('cascade');

            $table->bigInteger('quiz_session_id')->unsigned();
            $table->foreign('quiz_session_id')
                ->references('id')
                ->on('quiz_sessions')
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
        Schema::dropIfExists('quiz_short_answer_responses');
    }
}
