<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class QuizMultipleChoiceQuestion extends Model implements QuizQuestion
{
    public function quiz(){
        return $this->belongsTo('App\Quiz');
    }
    public function entries(){
        return $this->hasMany('App\QuizMultipleChoiceEntry','quiz_multiple_choice_question_id','id');
    }

    public function answer(){

    }

    function getTypeAttribute()
    {
        return 'multipleChoice';
    }
}