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
        $ses->calculateMaxScore();
        $ses->calculateScore();
        \EntityManager::persist($ses);
        \EntityManager::flush();
    }
}
