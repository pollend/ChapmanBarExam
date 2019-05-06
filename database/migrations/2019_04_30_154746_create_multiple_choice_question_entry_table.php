<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMultipleChoiceQuestionEntryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multiple_choice', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallInteger('order');
            $table->text('content');

            $table->bigInteger('multiple_choice_question_id')->unsigned();
            $table->foreign('multiple_choice_question_id')
                ->references('id')
                ->on('multiple_choice_question')
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
        Schema::dropIfExists('multiple_choice');
    }
}
