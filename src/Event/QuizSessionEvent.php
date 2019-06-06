<?php


namespace App\Event;


use App\Entity\Quiz;
use App\Entity\QuizSession;
use Symfony\Contracts\EventDispatcher\Event;

class QuizSessionEvent extends Event
{
    public const FINISH = 'quiz.session.finish';

    protected $session;

    public function __construct(QuizSession $session)
    {
        $this->$session = $session;
    }

    /**
     * @return QuizSession
     */
    public function getSession()
    {
        return $this->session;
    }
}