<?php

namespace App\Jobs;

use App\Entities\MultipleChoiceQuestion;
use App\Entities\QuizResponse;
use App\Entities\QuizSession;
use App\Repositories\QuizSessionRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;

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
        $maxScore = 0;
        $score = 0;

        $responses = $this->session->getResponses();
        $quiz = $this->session->getQuiz();
        $questions = $quiz->getQuestions();

//        Collection::make($this->session->getResponses())
            

        foreach ($questions as $question){
            if($question instanceof MultipleChoiceQuestion){
                $maxScore++;

            }
        }





    }
}
