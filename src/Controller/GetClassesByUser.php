<?php


namespace App\Controller;


use App\Entity\Classroom;
use App\Entity\User;
use App\Repository\ClassroomRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Security\Core\Security;

class GetClassesByUser
{

    private $entityManager;
    private $security;
    private $context;


    public function __construct(Security $security, EntityManagerInterface $entityManager, RequestContext $context)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->context = $context;
    }

    /**
     * @param $user_id
     * @return mixed
     * @throws \Exception
     */
    public function __invoke($user_id)
    {
        /** @var ClassroomRepository $classroomRepository */
        $classroomRepository = $this->entityManager->getRepository(Classroom::class);
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        /** @var User $targetUser */
        $user = $this->security->getUser();
        /** @var User $targetUser */
        $targetUser = $userRepository->find($user_id);
        $this->context->setParameter('user_id', $user_id);
        if($targetUser === $user || $this->security->isGranted(User::ROLE_ADMIN)){
            return $classroomRepository->byUser($targetUser);
        }
        throw new \Exception("Failed To Load Classrooms");
    }
}