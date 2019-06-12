<?php

namespace App\Event;

use App\Entity\QuizSession;
use JMS\Serializer\Annotation as JMS;
use Symfony\Contracts\EventDispatcher\Event;

class QuestionResultsEvent extends Event
{
    const QUESTION_RESULTS = 'app.question.result';

    /**
     * @JMS\Groups({"detail"})
     */
    protected $score;
    /**
     * @JMS\Groups({"detail"})
     */
    protected $maxScore;

    /**
     * @JMS\Groups({"result_set_questions"})
     */
    protected $questions;

    /**
     * @JMS\Groups({"result_set_session"})
     */
    protected $session;

    public function __construct(QuizSession $session, $questions = null)
    {
        $this->questions = $questions;
        $this->session = $session;
    }

    /**
     * @return QuizSession
     */
    public function getSession(): QuizSession
    {
        return $this->session;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @return mixed
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * @param mixed $score
     */
    public function setScore($score): void
    {
        $this->score = $score;
    }

    /**
     * @return mixed
     */
    public function getMaxScore()
    {
        return $this->maxScore;
    }

    /**
     * @param mixed $maxScore
     */
    public function setMaxScore($maxScore): void
    {
        $this->maxScore = $maxScore;
    }
}
