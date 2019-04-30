<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizSessionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_session_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->bigInteger('quiz_session_id')->unsigned();
            $table->foreign('quiz_session_id')
                ->references('id')
                ->on('quiz_sessions')
                ->onDelete('cascade');

            $table->json('response');

            $table->bigInteger('quiz_question_id')->unsigned();
            $table->foreign('quiz_question_id')
                ->references('id')
                ->on('quiz_questions')
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
        Schema::dropIfExists('quiz_session_answers');
    }
}
