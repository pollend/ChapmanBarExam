<?php


namespace App\Controller;


use App\Entity\MultipleChoiceEntry;
use App\Entity\MultipleChoiceQuestion;
use App\Entity\MultipleChoiceResponse;
use App\Entity\QuizQuestion;
use App\Entity\QuizResponse;
use App\Entity\QuizSession;
use App\Event\QuestionResultsEvent;
use App\Repository\MultipleChoiceEntryRepository;
use App\Repository\QuestionRepository;
use App\Repository\QuizResponseRepository;
use App\Security\QuizSessionVoter;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CreateQuestionBySessionAndPageAction
{
    private $entityManager;
    private $security;
    private $context;
    private $dispatcher;

    public function __construct(Security $security, EntityManagerInterface $entityManager, RequestContext $context, EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->context = $context;
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(QuizSession $session, Request $request, $page)
    {
        /** @var QuestionRepository $questionRepository */
        $questionRepository = $this->entityManager->getRepository(QuizQuestion::class);

        /** @var QuizResponseRepository $responseRepository */
        $responseRepository = $this->entityManager->getRepository(QuizResponse::class);

        /** @var MultipleChoiceEntryRepository $multipleChoiceEntryRepository */
        $multipleChoiceEntryRepository = $this->entityManager->getRepository(MultipleChoiceEntry::class);

        if ($session->getCurrentPage() != $page) {
            throw  new \Exception("Page does not match");
        }

        $quiz = $session->getQuiz();
        if ($session->getCurrentPage() == $page) {

            $groups = Collection::make($questionRepository->getUniqueGroups($quiz));
            $group = $groups[$page];

            if ($request->getMethod() == Request::METHOD_POST) {
                $session->setCurrentPage($session->getCurrentPage() + 1);
                if ($session->getCurrentPage() > $groups->keys()->max()) {
                    $session->setSubmittedAt(Carbon::now());

                    // calculate scores for session
                    $event = new QuestionResultsEvent($session, $session->getQuiz()->getQuestions());
                    $this->dispatcher->dispatch($event, QuestionResultsEvent::QUESTION_RESULTS);
                    $session->setScore($event->getScore());

                }
                $this->entityManager->persist($session);
            }

            $questions = Collection::make($questionRepository->filterByGroup($group, $quiz))->keyBy(
                function ($q) {
                    return $q->getId();
                });

            $data = json_decode($request->getContent(), true);
            foreach ($data as $key => $target) {
                if ($questions->has($key)) {
                    $question = $questions[$key];
                    $response = $responseRepository->filterResponseBySessionAndQuestion($session, $question);
                    if ($question instanceof MultipleChoiceQuestion) {
                        $response = $response ? $response : new MultipleChoiceResponse();
                        /** @var MultipleChoiceEntry $e */
                        foreach ($multipleChoiceEntryRepository->getEntriesForQuestion($question) as $e) {
                            if ($e->getId() == $target) {
                                $response->setChoice($e);
                            }
                        }
                    }

                    $response->setQuestion($question);
                    $response->setSession($session);
                    $this->entityManager->persist($response);
                }
            }
        }

        $this->entityManager->flush();
        return $session;
    }
}