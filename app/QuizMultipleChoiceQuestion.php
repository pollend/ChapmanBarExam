<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class QuizMultipleChoiceQuestion extends Model implements QuizQuestion
{
    public function entries(){
        return $this->hasMany('App\QuizMultipleChoiceEntry','quiz_multiple_choice_question_id','id');
    }
}