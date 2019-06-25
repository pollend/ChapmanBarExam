<?php

namespace App\Command;

use App\Entity\Quiz;
use App\Entity\QuizSession;
use App\Event\MaxScoreEvent;
use App\Event\QuestionResultsEvent;
use App\Repository\QuizRepository;
use App\Repository\QuizSessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RecalculateScoresForQuizzes extends Command
{
    protected static $defaultName = 'app:re-calculate-scores';

    private $entityManager;
    private $dispatcher;

    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher)
    {
        parent::__construct(null);
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
    }

    protected function configure()
    {
        $this->setDescription("Calculate Scores for quizzes and sessions")
            ->addArgument('batch-size', InputArgument::OPTIONAL, 'batch size to process.', 200);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var QuizSessionRepository $quizSessionRepository */
        $quizSessionRepository = $this->entityManager->getRepository(QuizSession::class);

        /** @var QuizRepository $quizRepository */
        $quizRepository = $this->entityManager->getRepository(Quiz::class);

        $io = new SymfonyStyle($input, $output);
        $batchSize = $input->getArgument('batch-size');

        $i = 0;
        $quizQuery = $quizRepository->createQueryBuilder('q');
        foreach ($quizQuery->getQuery()->iterate() as $row) {
            /** @var Quiz $quiz */
            $quiz = $row[0];
            $event = new MaxScoreEvent($quiz->getQuestions());
            $this->dispatcher->dispatch($event, MaxScoreEvent::MAX_SCORE);
            $quiz->setMaxScore($event->getMaxScore());
            $this->entityManager->persist($quiz);
            if (($i % $batchSize) === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
            ++$i;
        }
        $this->entityManager->flush();

        $i = 0;
        $sessionQuery = $quizSessionRepository->createQueryBuilder('q');
        foreach ($sessionQuery->getQuery()->iterate() as $row) {
            /** @var QuizSession $session */
            $session = $row[0];
            $event = new QuestionResultsEvent($session, $session->getQuiz()->getQuestions());
            $this->dispatcher->dispatch($event, QuestionResultsEvent::QUESTION_RESULTS);
            $session->setScore($event->getScore());
            $this->entityManager->persist($session);
            if (($i % $batchSize) === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
            ++$i;
        }
        $this->entityManager->flush();
    }
}