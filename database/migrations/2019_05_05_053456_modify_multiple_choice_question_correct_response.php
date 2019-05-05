<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyMultipleChoiceQuestionCorrectResponse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quiz_multiple_choice_questions', function (Blueprint $table) {
            $table->bigInteger('quiz_multiple_choice_entry_id')->unsigned()->nullable();
            $table->foreign('quiz_multiple_choice_entry_id')
                ->references('id')
                ->on('quiz_multiple_choice_entries')
                ->onDelete('Set NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quiz_multiple_choice_questions', function (Blueprint $table) {
            $table->dropColumn('quiz_multiple_choice_entries');
        });
    }
}
