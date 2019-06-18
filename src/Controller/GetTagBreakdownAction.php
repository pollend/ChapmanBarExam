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
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;


class GetTagBreakdownAction
{
    private $entityManager;
    private $security;
    private $dispatcher;
    private $serializer;

    public function __construct(SerializerInterface $serializer, EventDispatcherInterface $dispatcher, Security $security, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->dispatcher = $dispatcher;
        $this->serializer = $serializer;
    }

    public function __invoke(QuizSession $session)
    {
        /** @var QuestionTagRepository $questionTagRepository */
        $questionTagRepository = $this->entityManager->getRepository(QuestionTag::class);
        /** @var QuestionRepository $questionRepository */
        $questionRepository = $this->entityManager->getRepository(QuizQuestion::class);


        $tags = Collection::make($questionTagRepository->getUniqueTagsForQuiz($session->getQuiz()))->keyBy(function ($tag) {
            return $tag->getId();
        });
        $breakdown = Collection::make();
        /** @var QuestionTag $tag */
        foreach ($tags as $tag) {
            $questionsByTag = $questionRepository->filterByTag($session->getQuiz(), $tag);
            $event = new QuestionResultsEvent($session, $questionsByTag);
            $this->dispatcher->dispatch($event, QuestionResultsEvent::QUESTION_RESULTS);
            $breakdown->put($tag->getId(), new TagPayload($event, $tag));
        }

        return new JsonResponse($this->serializer->normalize($breakdown->values(), null, ['groups' => ['tag_payload', 'question_result:score', 'tag:get']]));

    }
}

