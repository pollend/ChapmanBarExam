<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuizSession extends Model
{

    public function owner()
    {
        return $this->morphTo();
    }

    public function sessionAnswers()
    {
        return $this->hasMany('App\QuizSessionAnswer', 'quiz_session_id', 'id');
    }
}
