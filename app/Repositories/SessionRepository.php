<?php


namespace App\Repositories;


use App\Exceptions\QuizClosedException;
use App\Exceptions\SessionInProgressException;
use App\QuizSession;

class SessionRepository implements SessionRepositoryInterface
{
    private $quizRepository;

    public function __construct(QuizRepositoryInterface $quizRepository)
    {
        $this->quizRepository = $quizRepository;

    }

    public function calculateScore(){

    }

    public function startSession($user, $quiz)
    {
        if ($this->getActiveSession($user) !== null)
            throw new SessionInProgressException();

        if (!$this->quizRepository->isOpen($quiz, $user))
            throw new QuizClosedException();

        $session = new QuizSession();
        $session->quiz_id = $quiz->id;
        $session->owner_id = $user->id;
        $session->saveOrFail();
        return $session;
    }

    public function getActiveSession($user){
        return QuizSession::query()
            ->where('owner_id',$user->id)
            ->where('submitted_at',null)
            ->first();
    }
}