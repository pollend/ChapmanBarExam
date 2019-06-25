<?php


namespace App\Event;


use App\Entity\Quiz;

class MaxScoreEvent
{
    const MAX_SCORE = 'app.questions.maxscore';

    private $questions;

    private $maxScore;

    public function __construct($questions)
    {
        $this->questions = $questions;

    }

    /**
     * @param mixed $maxScore
     */
    public function setMaxScore($maxScore): void
    {
        $this->maxScore = $maxScore;
    }

    /**
     * @return mixed
     */
    public function getMaxScore()
    {
        return $this->maxScore;
    }

    /**
     * @return mixed
     */
    public function getQuestions()
    {
        return $this->questions;
    }
}