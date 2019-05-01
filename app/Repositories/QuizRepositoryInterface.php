<?php


namespace App\Repositories;


interface QuizRepositoryInterface
{
    function isOpen($quiz, $user);

    public function getQuestions($quiz);
}