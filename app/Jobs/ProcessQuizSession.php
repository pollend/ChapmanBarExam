<?php

namespace App\Jobs;

use App\Entities\Quiz;
use App\Entities\QuizSession;
use App\Repositories\QuizRepository;
use App\Repositories\QuizSessionRepository;
use App\Utility\QuestionResult;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
     * @param QuizSession $session
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
        $questions = $ses->getQuiz()->getQuestions();

        $result = new QuestionResult($questions,$ses);
        $ses->setMaxScore($result->getMaxScore());
        $ses->setScore($result->getScore());


        \EntityManager::persist($ses);
        \EntityManager::flush();
    }
}
