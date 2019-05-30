<?php


namespace App\Utility;


use App\Entities\MultipleChoiceQuestion;
use App\Entities\MultipleChoiceResponse;
use App\Entities\QuizResponse;
use App\Entities\QuizSession;
use App\Repositories\QuizResponseRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use NumberFormatter;

class QuestionResult
{

    private $questions;

    private $maxScore;

    private $score;

    private  $session;

    public function __construct($questions,QuizSession $session)
    {
        $this->questions = $questions;
        $this->session = $session;

        $this->maxScore = 0;
        $this->score = 0;

        /** @var QuizResponseRepository $responseRepository */
        $responseRepository = \EntityManager::getRepository(QuizResponse::class);

        $responses = Collection::make($responseRepository->filterResponsesBySessionAndQuestions($this->session, $this->questions))
            ->keyBy(function ($item) {

                return $item->getQuestion()->getId();
            });

        foreach ($this->questions as $question) {
            if ($question instanceof MultipleChoiceQuestion) {
                $this->maxScore++;

                if (Arr::exists($responses, $question->getId())) {
                    /** @var MultipleChoiceResponse $response */
                    $response = $responses[$question->getId()];
                    if ($question->getCorrectEntry() === $response->getChoice()) {
                        $this->score++;
                    }
                }
            }
        }
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
    public function getMaxScore()
    {
        return $this->maxScore;
    }

    /**
     * @return array
     */
    public function getQuestions(): array
    {
        return $this->questions;
    }

}