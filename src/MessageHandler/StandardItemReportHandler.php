<?php


namespace App\MessageHandler;


use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Classroom;
use App\Entity\MultipleChoiceEntry;
use App\Entity\MultipleChoiceQuestion;
use App\Entity\MultipleChoiceResponse;
use App\Entity\Quiz;
use App\Entity\QuizQuestion;
use App\Entity\QuizSession;
use App\Message\StandardItemReport;
use App\Repository\ClassroomRepository;
use App\Repository\MultipleChoiceResponseRepository;
use App\Repository\QuizRepository;
use App\Repository\QuizSessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Collection;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class StandardItemReportHandler implements MessageHandlerInterface
{
    private $entityManager;
    private $cache;
    /** @var QuizRepository */
    private $quizRepository;
    /** @var ClassroomRepository  */
    private $classroomRepository;
    /** @var MultipleChoiceResponseRepository */
    private $multipleChoiceRepository;
    /** @var QuizSessionRepository */
    private $quizSessionRepository ;
    private $objectNormalizer;
    private $iriConverter;

    public function __construct(CacheInterface $cache, IriConverterInterface $iriConverter, NormalizerInterface $objectNormalizer, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->cache = $cache;
        $this->objectNormalizer = $objectNormalizer;
        $this->iriConverter = $iriConverter;

        $this->quizRepository = $this->entityManager->getRepository(Quiz::class);
        $this->classroomRepository = $this->entityManager->getRepository(Classroom::class);
        $this->multipleChoiceRepository = $this->entityManager->getRepository(MultipleChoiceResponse::class);
        $this->quizSessionRepository = $this->entityManager->getRepository(QuizSession::class);


    }

    public function __invoke(StandardItemReport $standardReport)
    {

        /** @var Quiz $quiz */
        if ($quiz = $this->quizRepository->find($standardReport->getQuiz()->getId())) {
            /** @var Classroom $classroom */
            if ($classroom = $this->classroomRepository->find($standardReport->getClassroom()->getId())) {
                $sessions = Collection::make($this->quizSessionRepository->getSessionsByClassAndQuiz($quiz, $classroom));
                $this->cache->get('report_' . $quiz->getId() . '_' . $classroom->getId(), function (ItemInterface $item) use ($sessions, $quiz, $classroom) {
                    $item->expiresAfter(3600);
                    $targetSessions = Collection::make();

                    /**
                     * @var int $uid
                     * @var Collection $value
                     */
                    foreach ($sessions->groupBy(function ($item, $key) {
                        return $item->getOwner()->getId();
                    }) as $uid => $value) {
                        $targetSessions->add($value->sortBy(function ($session, $key) {
                            return -$session->getScore();
                        })[0]);
                    }

                    $targetSessions = $targetSessions->keyBy(function ($session) {
                        return $session->getId();
                    });

                    $result = [];
                    /** @var QuizQuestion $question */
                    foreach ($quiz->getQuestions() as $question) {
                        if ($question instanceof MultipleChoiceQuestion) {
                            $entries = Collection::make($question->getEntries())->keyBy(function ($item) {
                                /** @var MultipleChoiceEntry $entry */
                                $entry = $item;
                                return $entry->getId();
                            });

                            $responseResult = [];
                            $nonResponseResult = [];
                            $responses = $this->multipleChoiceRepository->filterByQuestionAndSessions($question, $targetSessions->values()->toArray());

                            $responsesBySession = Collection::make($responses)->keyBy(function ($value) {
                                return $value->getSession()->getId();
                            });

                            $targetSessions->each(function ($value, $key) use ($responsesBySession, &$responseResult, &$nonResponseResult) {
                                /** @var QuizSession $session */
                                $session = $value;
                                if ($responsesBySession->has($key)) {
                                    /** @var MultipleChoiceResponse $response */
                                    $response = $responsesBySession->get($key);
                                    $responseResult [$this->iriConverter->getIriFromItem($response->getChoice())][] = $this->objectNormalizer->normalize($session, 'jsonld', ['groups' => ["quiz_session:get:report"]]);
                                } else {
                                    $nonResponseResult[] = $this->objectNormalizer->normalize($session, 'jsonld', ['groups' => ["quiz_session:get:report"]]);
                                }
                            });

                            $result[] = [
                                'nonResponse' => $nonResponseResult,
                                'responseByUser' => $responseResult,
                                'correctChoice' => $this->objectNormalizer->normalize($question->getCorrectEntry(), 'jsonld', ['groups' => ["tag:get"]]),
                                'choices' => $entries->values()->map(function ($item, $key) {
                                    return $this->objectNormalizer->normalize($item, 'jsonld', ['groups' => ["tag:get"]]);
                                }),
                            ];
                        }
                    }
                    return $result;
                });
//                $this->cache->get()
//                $this->cache->s
//                $this->cache->get()
            }
        }
    }
}
