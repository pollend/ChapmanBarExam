<?php

namespace App\Jobs;

use App\Entities\MultipleChoiceQuestion;
use App\Entities\MultipleChoiceResponse;
use App\Entities\Quiz;
use App\Entities\QuizSession;
use App\Repositories\QuizRepository;
use App\Repositories\QuizSessionRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Calculate the score for the quiz. Implementation can be more complex in the future for groups or what not.
 * @package App\Jobs
 */
class ProcessQuizSession implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $session;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(QuizSession $session)
    {
        $this->session = $session;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @var QuizSessionRepository $quizSessionRepository */
        $quizSessionRepository = \EntityManager::getRepository(QuizSession::class);
        /** @var QuizRepository $quizRepository */
        $quizRepository = \EntityManager::getRepository(Quiz::class);

        /** @var QuizSession $ses */
        $ses = $quizSessionRepository->find($this->session->getId());

        $maxScore = 0;
        $score = 0;

        $responses = Collection::make($ses->getResponses())->keyBy(function ($item) {
            return $item->getQuestion()->getId();
        });

        /** @var Quiz $quiz */
        $quiz = $ses->getQuiz();
        $questions = $quizRepository->find($quiz->getId())->getQuestions();
        foreach ($questions as $question) {
            if ($question instanceof MultipleChoiceQuestion) {
                $maxScore++;
                if (Arr::exists($responses, $question->getId())) {
                    /** @var MultipleChoiceResponse $multipleChoiceResponse */
                    $multipleChoiceResponse = $responses[$question->getId()];
                    if ($question->getCorrectEntry() === $multipleChoiceResponse->getChoice()) {
                        $score++;
                    }
                }
            }
        }


        $ses->setMaxScore($maxScore);
        $ses->setScore($score);
        \EntityManager::persist($ses);
        \EntityManager::flush();
    }
}
