<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMultipleChoiceResponseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_multiple_choice_responses', function (Blueprint $table) {
            $table->bigIncrements('id');


            $table->bigInteger('quiz_multiple_choice_entry_id')->unsigned();
            $table->foreign('quiz_multiple_choice_entry_id')
                ->references('id')
                ->on('quiz_multiple_choice_entries')
                ->onDelete('cascade');

            $table->bigInteger('quiz_session_id')->unsigned();
            $table->foreign('quiz_session_id')
                ->references('id')
                ->on('quiz_sessions')
                ->onDelete('cascade');


            $table->bigInteger('quiz_multiple_choice_question_id')->unsigned();
            $table->foreign('quiz_multiple_choice_question_id')
                ->references('id')
                ->on('quiz_multiple_choice_questions')
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
        Schema::dropIfExists('quiz_multiple_choice_responses');
    }
}
