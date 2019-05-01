<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class QuizShortAnswerQuestion extends Model implements QuizQuestion
{

    function getTypeAttribute()
    {
        return 'shortAnswer';
    }
}