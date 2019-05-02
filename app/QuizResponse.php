<?php


namespace App;


interface QuizResponse
{
    public function scopeBySession($query, $session);
}