<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    public function questions(){
        return $this->hasMany('App\QuizQuestion','quiz_id','id');
    }

    public function quizSessions(){
        return $this->hasMany('App\QuizSession','quiz_id','id');
    }
}
