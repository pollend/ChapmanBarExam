<?php

namespace App\EventListener;

use App\Entity\MultipleChoiceQuestion;
use App\Entity\MultipleChoiceResponse;
use App\Entity\QuizQuestion;
use App\Entity\QuizResponse;
use App\Event\MaxScoreEvent;
use App\Event\QuestionEvent;
use App\Event\QuestionResultsEvent;
use App\Event\QuizResultEvent;
use App\Repository\QuizResponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class QuizSubscriber implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            QuestionResultsEvent::QUESTION_RESULTS => 'calculateResultsSet',
            MaxScoreEvent::MAX_SCORE => 'calculateMaxScore'
        ];
    }

    public function calculateMaxScore(MaxScoreEvent $event){
        $score = 0;
        foreach ($event->getQuestions() as $question){
            if ($question instanceof MultipleChoiceQuestion) {
                $score++;
            }
        }
        $event->setMaxScore($score);
    }


    public function calculateResultsSet(QuestionResultsEvent $event)
    {
        /** @var QuizResponseRepository $responseRepo */
        $responseRepo = $this->em->getRepository(QuizResponse::class);

        $responses = Collection::make($responseRepo->filterResponsesBySessionAndQuestions($event->getSession(), $event->getQuestions()))
            ->keyBy(function ($item) {
                return $item->getQuestion()->getId();
            });
        $score = 0;
        $questions = $event->getQuestions();


        $maxScoreEvent = new MaxScoreEvent($questions);
        $this->calculateMaxScore($maxScoreEvent);
        $event->setMaxScore($maxScoreEvent->getMaxScore());

        foreach ($questions as $question) {
            if ($question instanceof MultipleChoiceQuestion) {
                if (Arr::exists($responses, $question->getId())) {
                    /** @var MultipleChoiceResponse $response */
                    $response = $responses[$question->getId()];
                    if ($question->getCorrectEntry() === $response->getChoice()) {
                        ++$score;
                    }
                }
            }
        }
        $event->setScore($score);

    }
}
