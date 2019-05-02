<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class QuizMultipleChoiceEntry extends Model
{
    public $timestamps = false;

    public function quizMultipleChoiceQuestion(){
        return $this->belongsTo('App\QuizMultipleChoiceQuestion');
    }

    public function quizMultipleChoiceRespone(){
        return $this->hasMany('QuizMultipleChoiceAnswerResponse');
    }
}