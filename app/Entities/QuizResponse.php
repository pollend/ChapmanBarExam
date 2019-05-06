<?php


namespace App;


abstract class QuizResponse
{
    abstract function scopeBySession($query, $session);
}