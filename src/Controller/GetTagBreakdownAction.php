<?php


namespace App\Controller;


use App\Entity\QuestionTag;
use App\Entity\QuizQuestion;
use App\Entity\QuizSession;
use App\Event\QuestionResultsEvent;
use App\Repository\QuestionRepository;
use App\Repository\QuestionTagRepository;
use App\utility\TagPayload;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;


class GetTagBreakdownAction
{
    private $entityManager;
    private $security;
    private $dispatcher;
    private $serializer;
    private $cache;

    private $questionTagRepository;
    private $questionRepository;

    public function __construct(CacheInterface $cache,SerializerInterface $serializer, EventDispatcherInterface $dispatcher, Security $security, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->dispatcher = $dispatcher;
        $this->serializer = $serializer;
        $this->cache = $cache;

        /** @var QuestionTagRepository $questionTagRepository */
        $this->questionTagRepository = $this->entityManager->getRepository(QuestionTag::class);
        /** @var QuestionRepository $questionRepository */
        $this->questionRepository = $this->entityManager->getRepository(QuizQuestion::class);


    }

    public function __invoke(QuizSession $session)
    {

        return new JsonResponse($this->cache->get('quiz_session_breakdown_' . $session->getId(), function (ItemInterface $item) use ($session){
            // expires after 3600 seconds
            $item->expiresAfter(3600);

            $tags = Collection::make($this->questionTagRepository->getUniqueTagsForQuiz($session->getQuiz()))->keyBy(function ($tag) {
                return $tag->getId();
            });
            $breakdown = Collection::make();
            /** @var QuestionTag $tag */
            foreach ($tags as $tag) {
                $questionsByTag = $this->questionRepository->filterByTag($session->getQuiz(), $tag);
                $event = new QuestionResultsEvent($session, $questionsByTag);
                $this->dispatcher->dispatch($event, QuestionResultsEvent::QUESTION_RESULTS);
                $breakdown->put($tag->getId(), [
                    'maxScore' => $event->getMaxScore(),
                    'score' => $event->getScore(),
                    'tag' => $tag->getName()
                ]);
            }
            return $this->serializer->normalize($breakdown->values(), null, ['groups' => ['tag_payload', 'question_result:score', 'tag:get']]);
        }));
    }
}

