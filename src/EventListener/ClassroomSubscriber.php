<?php


namespace App\EventListener;


use App\Entity\User;
use App\Entity\UserWhitelist;
use App\Event\ClassroomUpdateUsersByWhiteListEvent;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ClassroomSubscriber implements EventSubscriberInterface
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
            ClassroomUpdateUsersByWhiteListEvent::CLASSROOM_UPDATE_USERLIST => [
                ['onClassroomUpdateWhitelist', 0]
            ]
        ];
    }

    public function onClassroomUpdateWhitelist(ClassroomUpdateUsersByWhiteListEvent $event)
    {
        $classroom = $event->getClassroom();

        /** @var UserRepository $userRepository */
        $userRepository = $this->em->getRepository(User::class);

        /** @var UserWhitelist $whiteList */
        foreach ($classroom->getEmailWhitelist() as $whiteList) {
            $email = $whiteList->getEmail();
            if ($user = $userRepository->findOneBy(['email' => $email])) {
                $classroom = $whiteList->getClassroom();
                $classroom->getUsers()->add($user);
                $this->em->persist($classroom);
            }
        }

        $this->em->flush();
    }
}