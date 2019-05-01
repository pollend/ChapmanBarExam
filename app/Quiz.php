<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $dates = ['close_date'];

    public function multipleChoiceQuestions(){
        return $this->hasMany('App\QuizMultipleChoiceQuestion','quiz_id','id');
    }

    public function shortAnswerQuestions(){
        return $this->hasMany('App\QuizShortAnswerQuestion','quiz_id','id');
    }
}
