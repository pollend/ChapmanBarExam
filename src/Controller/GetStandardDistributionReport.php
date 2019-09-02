<?php


namespace App\Controller;


use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Classroom;
use App\Entity\Quiz;
use App\Entity\QuizSession;
use App\Entity\User;
use App\Event\MaxScoreEvent;
use App\Event\QuestionResultsEvent;
use App\Event\QuizResultEvent;
use App\Repository\QuizRepository;
use App\Repository\QuizSessionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class GetStandardDistributionReport
{
    private $entityManager;
    private $iriConverter;
    private $dispatcher;
    public function __construct(EventDispatcherInterface $dispatcher,EntityManagerInterface $entityManager,IriConverterInterface $iriConverter)
    {
        $this->dispatcher = $dispatcher;
        $this->entityManager = $entityManager;
        $this->iriConverter = $iriConverter;
    }

    public function __invoke(Classroom $classroom,Request $request, $report_id)
    {
        /** @var QuizSessionRepository $quizSessionRepository */
        $quizSessionRepository = $this->entityManager->getRepository(QuizSession::class);

        /** @var QuizRepository $quizSessionRepository */
        $quizRepository = $this->entityManager->getRepository(Quiz::class);

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        /** @var Quiz $quiz */
        if ($quiz = $quizRepository->find($report_id)) {
            $sessions = Collection::make($quizSessionRepository->getSessionsByClassAndQuiz($quiz,$classroom));

            $processed_scores = [];

            $sessionsByOwner = $sessions->groupBy(function ($item,$key) {
               return $item->getOwner()->getId();
            });


            foreach ($sessionsByOwner as $userId => $sessions) {

                $processed_scores[$userId]['max'] = $sessions->max(function ($session) {
                    /** @var $session QuizSession */
                    $scoreEvent = new QuestionResultsEvent($session,$session->getQuiz()->getQuestions());
                    $this->dispatcher->dispatch($scoreEvent,QuestionResultsEvent::QUESTION_RESULTS);
                    return $scoreEvent->getScore();
                });
                $processed_scores[$userId]['avg'] = $sessions->average(function ($session) {
                    /** @var $session QuizSession */
                    $scoreEvent = new QuestionResultsEvent($session,$session->getQuiz()->getQuestions());
                    $this->dispatcher->dispatch($scoreEvent,QuestionResultsEvent::QUESTION_RESULTS);
                    return $scoreEvent->getScore();
                });
            }

            $result = [];
            foreach ($processed_scores as $user_id => $score){
                $s = $sessionsByOwner->get($user_id);
                /** @var User $user */
                $user = $userRepository->find($user_id);

                $result[] = [
                  'maxRawScore' => $score['max'],
                  'avgRawScore' => $score['avg'],
                  'maxScore' => $quiz->getMaxScore(),
                  'attempts' => count($s),
                  'user' => $this->iriConverter->getIriFromItem($user)
                ];

            }
            return $result;

        }
        return [];

    }

}
