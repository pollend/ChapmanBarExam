<?php


namespace App\Event;


use App\Entity\Quiz;
use Symfony\Contracts\EventDispatcher\Event;

class QuizResultEvent extends Event
{
    const QUIZ = 'app.quiz.result';

    private $quiz;
    private $score;

    public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz;
    }

    /**
     * @return Quiz
     */
    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }

    public function setMaxScore(int $score)
    {
        $this->score = $score;
    }

    public function getMaxScore()
    {
        return $this->score;
    }
}
