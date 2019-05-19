<?php

namespace App\Console\Commands;

use App\Entities\QuizSession;
use App\Jobs\ProcessQuizSession;
use App\Repositories\QuizSessionRepository;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Queue\Jobs\Job;

class RecalculateScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chapmanbar:scores:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs through all the sessions and updates all the scores.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $i = 0;
        /** @var QuizSessionRepository $quizSessionRepository */
        $quizSessionRepository = \EntityManager::getRepository(QuizSession::class);
        $iterator = $quizSessionRepository->createQueryBuilder('q')->getQuery()->iterate();
        /** @var QuizSession $row */
        foreach ($iterator as $row){
            ProcessQuizSession::dispatch($row[0]);
        }

    }
}
