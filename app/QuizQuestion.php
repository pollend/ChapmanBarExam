<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    public function quiz(){
        return $this->belongsTo('App\Quiz');
    }

    /**
     * Get the comments for the blog post.
     */
    public function sessionAnswers()
    {
        return $this->hasMany('App\QuizSessionAnswer','quiz_session_id','id');
    }


}
