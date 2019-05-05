<?php


namespace App\Repositories;


use App\Quiz;
use Illuminate\Support\Collection;

interface QuizRepositoryInterface
{
    function isOpen($quiz, $user);

    function getQuestions($quiz,\Closure $callback = null);

    function getGroupedQuestions($quiz);

    function getUnionedQuestions(\Closure $query = null);

    /**
     * @param Quiz $quiz
     * @return Collection
     */
    function getGroups(Quiz $quiz);
}