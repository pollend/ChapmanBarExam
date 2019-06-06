<?php


namespace App\EventListener;


use App\Entity\QuizResponse;
use App\Event\QuizSessionEvent;
use App\Repository\QuizResponseRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        return [QuizSessionEvent::FINISH => 'onFinish'];
    }

    public function onFinish(QuizSessionEvent $event){

        $session = $event->getSession();

        /** @var QuizResponseRepository $responseRepo */
        $responseRepo = $this->em->getRepository(QuizResponse::class);


    }
}