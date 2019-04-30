<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuizSession extends Model
{

    public function quiz(){
        return $this->belongsTo('App\Quiz');
    }

    public function owner(){
        return $this->belongsTo('App\User');
    }

    public function sessionAnswers()
    {
        return $this->hasMany('App\QuizSessionAnswer', 'quiz_session_id', 'id');
    }
}
