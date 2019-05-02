<?php


namespace App\Repositories;


use App\QuizSession;

interface SessionRepositoryInterface
{

    function startSession($user, $quiz);

    /**
     * @param $user
     * @return QuizSession
     */
    function getActiveSession($user);

}