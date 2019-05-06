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
        Schema::create('multiple_choice_response', function (Blueprint $table) {
            $table->bigIncrements('id');


            $table->bigInteger('multiple_choice_entry_id')->unsigned();
            $table->foreign('multiple_choice_entry_id')
                ->references('id')
                ->on('multiple_choice')
                ->onDelete('cascade');

            $table->bigInteger('quiz_session_id')->unsigned();
            $table->foreign('quiz_session_id')
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
        Schema::dropIfExists('multiple_choice_response');
    }
}
