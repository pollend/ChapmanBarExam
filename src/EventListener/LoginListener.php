<?php

namespace App\EventListener;

use App\Entity\Classroom;
use App\Entity\User;
use App\Repository\ClassroomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Collection;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoginListener implements EventSubscriberInterface
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
           Events::JWT_AUTHENTICATED => [
                ['onUpdateClassroomList', 0],
           ],
       ];
    }

    /**
     * update classroom list and add users.
     *
     * @param JWTAuthenticatedEvent $event
     */
    public function onUpdateClassroomList(JWTAuthenticatedEvent $event)
    {
        /** @var ClassroomRepository $classRepo */
        $classRepo = $this->em->getRepository(Classroom::class);

        /** @var User $user */
        $user = $event->getToken()->getUser();

        $inClasses = Collection::make($classRepo->byUser($user));
        /** @var Classroom $class */
        foreach ($classRepo->byEmail($user->getEmail()) as $class) {
            // if user is not in class add to class
            if (!$inClasses->contains($class)) {
                $class->getUsers()->add($user);
                $this->em->persist($class);
            }
        }
        $this->em->flush();
    }
}
