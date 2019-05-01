<?php


namespace App\Repositories;


use App\Quiz;

interface QuizRepositoryInterface
{
    function isOpen($quiz, $user);

    function getQuestions($quiz);

    function getGroups(Quiz $quiz);
}