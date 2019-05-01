<?php


namespace App\Repositories;


use Carbon\Carbon;

class QuizRepository implements QuizRepositoryInterface
{
    public function isOpen($quiz, $user)
    {
        if ($quiz->quizSessions()->where('owner_id', $user->id)->count() >= $quiz->attempts)
            return false;
        if($quiz->close_date > Carbon::today())
            return false;
        if ($quiz->is_open == false)
            return false;
        return true;
    }

    public function attempt_count($quiz, $user)
    {
        return $quiz->quizSessions()->where('owner_id', $user->id)->count();
    }

}