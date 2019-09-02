<?php


namespace App\MessageHandler;


use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Classroom;
use App\Entity\Quiz;
use App\Entity\QuizSession;
use App\Entity\User;
use App\Event\QuestionResultsEvent;
use App\JobStatus;
use App\Message\DistributionReport;
use App\Message\StandardItemReport;
use App\Repository\ClassroomRepository;
use App\Repository\MultipleChoiceResponseRepository;
use App\Repository\QuizRepository;
use App\Repository\QuizSessionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Collection;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class StandardDistributionReportHandler implements MessageHandlerInterface
{
    private $entityManager;
    private $cache;
    private $objectNormalizer;
    private $iriConverter;
    private $dispatcher;

    /** @var QuizSessionRepository */
    private $quizSessionRepository;
    /** @var QuizRepository */
    private $quizRepository;
    /** @var UserRepository */
    private $userRepository;
    /** @var ClassroomRepository */
    private $classroomRepository;

    public function __construct(CacheInterface $cache, EventDispatcherInterface $dispatcher, IriConverterInterface $iriConverter, NormalizerInterface $objectNormalizer, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->objectNormalizer = $objectNormalizer;
        $this->cache = $cache;
        $this->iriConverter = $iriConverter;
        $this->dispatcher = $dispatcher;

        $this->quizSessionRepository = $this->entityManager->getRepository(QuizSession::class);
        $this->quizRepository = $this->entityManager->getRepository(Quiz::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->classroomRepository = $this->entityManager->getRepository(Classroom::class);

    }

    public function __invoke(DistributionReport $report)
    {

        /** @var Quiz $quiz */
        if ($quiz = $this->quizRepository->find($report->getQuizId())) {
            /** @var Classroom $classroom */
            if ($classroom = $this->classroomRepository->find($report->getClassroomId())) {

                $this->cache->get(DistributionReport::getKey($report), function (ItemInterface $item) use ($quiz, $classroom, $report) {
                    $item->expiresAfter(3600);

                    $sessions = Collection::make($this->quizSessionRepository->getSessionsByClassAndQuiz($quiz, $classroom));
                    $processed_scores = [];

                    $sessionsByOwner = $sessions->groupBy(function ($item, $key) {
                        /** @var $item QuizSession */
                        return $item->getOwner()->getId();
                    });

                    $sessionCount = $sessionsByOwner->count();
                    $count = 0;

                    foreach ($sessionsByOwner as $userId => $sessions) {

                        $count++;
                        $this->cache->get(DistributionReport::getStatusKey($report), function (ItemInterface $item1) use ($sessionCount, $count) {
                            $item1->expiresAfter(500);
                            return new JobStatus('Collecting User Sessions',$count,$sessionCount);
                        }, INF);

                        $processed_scores[$userId]['max'] = $sessions->max(function ($session) {
                            /** @var $session QuizSession */
                            $scoreEvent = new QuestionResultsEvent($session, $session->getQuiz()->getQuestions());
                            $this->dispatcher->dispatch($scoreEvent, QuestionResultsEvent::QUESTION_RESULTS);
                            return $scoreEvent->getScore();
                        });
                        $processed_scores[$userId]['avg'] = $sessions->average(function ($session) {
                            /** @var $session QuizSession */
                            $scoreEvent = new QuestionResultsEvent($session, $session->getQuiz()->getQuestions());
                            $this->dispatcher->dispatch($scoreEvent, QuestionResultsEvent::QUESTION_RESULTS);
                            return $scoreEvent->getScore();
                        });
                    }

                    $result = [];
                    foreach ($processed_scores as $user_id => $score) {
                        $s = $sessionsByOwner->get($user_id);
                        /** @var User $user */
                        $user = $this->userRepository->find($user_id);

                        $result[] = [
                            'maxRawScore' => $score['max'],
                            'avgRawScore' => $score['avg'],
                            'maxScore' => $quiz->getMaxScore(),
                            'attempts' => count($s),
                            'user' => $this->iriConverter->getIriFromItem($user)
                        ];
                    }
                    return $result;
                });
                $this->cache->get(DistributionReport::getStatusKey($report), function (ItemInterface $item1) {
                    return new JobStatus('processing question', 1, 1,true);
                }, INF);
            }
        }
    }
}
