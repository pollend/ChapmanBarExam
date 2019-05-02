<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class QuizShortAnswerQuestion extends Model implements QuizQuestion
{

    public function answers(){
        return $this->hasMany('App\QuizShortAnswerResponse','quiz_short_answer_question_id', 'id');
    }

    function getTypeAttribute()
    {
        return 'shortAnswer';
    }
}