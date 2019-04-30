<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuizSessionAnswer extends Model
{
    public function question(){
        return $this->belongsTo('App\QuizQuestion');
    }

    public function session(){
        return $this->belongsTo('App\QuizSession');
    }
}
