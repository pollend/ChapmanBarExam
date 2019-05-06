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
        Schema::table('multiple_choice_question', function (Blueprint $table) {
            $table->bigInteger('multiple_choice_entry_id')->unsigned()->nullable();
            $table->foreign('multiple_choice_entry_id')
                ->references('id')
                ->on('multiple_choice')
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
        Schema::table('multiple_choice_question', function (Blueprint $table) {
            $table->dropColumn('multiple_choice_entry_id');
        });
    }
}
