<?php

namespace App\EventListener;

use App\Entity\MultipleChoiceQuestion;
use App\Entity\MultipleChoiceResponse;
use App\Entity\QuizResponse;
use App\Event\QuestionResultsEvent;
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
        return [QuestionResultsEvent::QUESTION_RESULTS => 'onCalculate'];
    }

    public function onCalculate(QuestionResultsEvent $event)
    {
        /** @var QuizResponseRepository $responseRepo */
        $responseRepo = $this->em->getRepository(QuizResponse::class);

        $responses = Collection::make($responseRepo->filterResponsesBySessionAndQuestions($event->getSession(), $event->getQuestions()))
            ->keyBy(function ($item) {
                return $item->getQuestion()->getId();
            });
        $maxScore = 0;
        $score = 0;
        foreach ($event->getQuestions() as $question) {
            if ($question instanceof MultipleChoiceQuestion) {
                ++$maxScore;
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
        $event->setMaxScore($maxScore);
    }
}
