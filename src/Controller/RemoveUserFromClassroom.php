<?php


namespace App\Controller;


use App\Entity\Classroom;
use App\Entity\User;
use App\Entity\UserWhitelist;
use App\Repository\UserRepository;
use App\Repository\UserWhitelistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class RemoveUserFromClassroom
{
    private $em;

    public function __construct( EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function __invoke(Classroom $classroom, Request $request, $user_id)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->em->getRepository(User::class);

        /** @var UserWhitelistRepository $userWhitelistRepository */
        $userWhitelistRepository = $this->em->getRepository(UserWhitelist::class);

        $users = $classroom->getUsers();
        /** @var User $user */
        if ($user = $userRepository->find($user_id)) {
            if ($users->contains($user)) {
                $users->removeElement($user);
                /** @var UserWhitelist $whitelist */
                if($whitelist = $userWhitelistRepository->byEmail($user->getEmail(),$classroom)){
                    $this->em->remove($whitelist);
                }
                $this->em->persist($classroom);
                $this->em->flush();
                return;
            }

        }
        throw new \Exception("User not found");
    }
}