<?php
namespace App\Controller;


use ApiPlatform\Core\Api\ResourceClassResolverInterface;
use ApiPlatform\Core\Bridge\Symfony\Routing\IriConverter;
use App\Entity\Classroom;
use App\Entity\MultipleChoiceEntry;
use App\Entity\MultipleChoiceQuestion;
use App\Entity\MultipleChoiceResponse;
use App\Entity\Quiz;
use App\Entity\QuizQuestion;
use App\Entity\QuizSession;
use App\JobRunning;
use App\Message\StandardItemReport;
use App\Repository\MultipleChoiceResponseRepository;
use App\Repository\QuizRepository;
use App\Repository\QuizSessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Collection;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Contracts\Cache\CacheInterface;

class GetStandardQuestionReport
{
    private $entityManager;
    protected $objectNormalizer;
    protected $context;
    protected $bus;
    private $cache;
    private $iriConverter;
    private $resourceClassResolver;

    public function __construct(ResourceClassResolverInterface $resourceClassResolver,AdapterInterface $cache,EntityManagerInterface $entityManager, ObjectNormalizer $objectNormalizer, RequestContext $context,MessageBusInterface $bus)
    {
        $this->bus = $bus;
        $this->entityManager = $entityManager;
        $this->objectNormalizer = $objectNormalizer;
        $this->context = $context;
        $this->cache = $cache;

        $this->resourceClassResolver  = $resourceClassResolver;
    }

    public function __invoke(Classroom $classroom, Request $request, $report_id)
    {
        /** @var QuizRepository $quizSessionRepository */
        $quizRepository = $this->entityManager->getRepository(Quiz::class);

        /** @var MultipleChoiceResponseRepository $multipleChoiceRepository */
        $multipleChoiceRepository = $this->entityManager->getRepository(MultipleChoiceResponse::class);

        /** @var QuizSessionRepository $quizSessionRepository */
        $quizSessionRepository = $this->entityManager->getRepository(QuizSession::class);

        $this->context->setParameter('report_id', $report_id);


        /** @var Quiz $quiz */
        if ($quiz = $quizRepository->find($report_id)) {

            $report = new StandardItemReport($classroom,$quiz);
            if($this->cache->hasItem($report->getKey())){
                return $this->cache->getItem($report->getKey())->get();
            }
            else{
//                $this->bus->dispatch($report);
            }

            return $report;


//            $sessions = Collection::make($quizSessionRepository->getSessionsByClassAndQuiz($quiz, $classroom));
//            $targetSessions = Collection::make();
//
//            /**
//             * @var int $uid
//             * @var Collection $value
//             */
//            foreach ($sessions->groupBy(function ($item, $key) {
//                return $item->getOwner()->getId();
//            }) as $uid => $value) {
//                $targetSessions->add($value->sortBy(function ($session, $key) {
//                    return -$session->getScore();
//                })[0]);
//            }
//
//            $targetSessions = $targetSessions->keyBy(function ($session) {
//                return $session->getId();
//            });
//
//
//            $result = [];
//            /** @var QuizQuestion $question */
//            foreach ($quiz->getQuestions() as $question) {
//                if ($question instanceof MultipleChoiceQuestion) {
//
//
//                    $entries = Collection::make($question->getEntries())->keyBy(function ($item) {
//                        /** @var MultipleChoiceEntry $entry */
//                        $entry = $item;
//                        return $entry->getId();
//                    });
//
//                    $responseResult = [];
//                    $nonResponseResult = [];
//                    $responses = $multipleChoiceRepository->filterByQuestionAndSessions($question, $targetSessions->values()->toArray());
//
//                    $responsesBySession = Collection::make($responses)->keyBy(function ($value) {
//                        return $value->getSession()->getId();
//                    });
//
//                    $targetSessions->each(function ($value, $key) use ($responsesBySession, &$responseResult, &$nonResponseResult) {
//                        /** @var QuizSession $session */
//                        $session = $value;
//                        if ($responsesBySession->has($key)) {
//                            /** @var MultipleChoiceResponse $response */
//                            $response = $responsesBySession->get($key);
//                            $responseResult [$this->iriConverter->getIriFromItem($response->getChoice())][] = $this->objectNormalizer->normalize($session, 'jsonld', ['groups' => ["quiz_session:get:report"]]);
//                        } else {
//                            $nonResponseResult[] = $this->objectNormalizer->normalize($session, 'jsonld', ['groups' => ["quiz_session:get:report"]]);
//                        }
//                    });
//
//                    $result[] = [
//                        'nonResponse' => $nonResponseResult,
//                        'responseByUser' => $responseResult,
//                        'correctChoice' => $this->objectNormalizer->normalize($question->getCorrectEntry(), 'jsonld', ['groups' => ["tag:get"]]),
//                        'choices' => $entries->values()->map(function ($item, $key) {
//                            return $this->objectNormalizer->normalize($item, 'jsonld', ['groups' => ["tag:get"]]);
//                        }),
//                    ];
//
//                }
//            }
//            return $result;
        }
        return [];
    }
}
