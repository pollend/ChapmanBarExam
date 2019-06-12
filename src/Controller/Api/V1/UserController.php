<?php

namespace App\Controller\Api\V1;

use App\Entity\QuizSession;
use App\Entity\User;
use App\Repository\QuizSessionRepository;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Route("/api/v1/")
 */
class UserController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("user/{user_id}/quiz/session",
     *     options = { "expose" = true },
     *     name="get_user_quiz_session")
     * @Rest\View(serializerGroups={"detail", "meta"})
     */
    public function getSessions($user_id)
    {
        /** @var UserRepository $userRepo */
        $userRepo = $this->getDoctrine()->getRepository(User::class);

        /** @var QuizSessionRepository $sessionRepo */
        $sessionRepo = $this->getDoctrine()->getRepository(QuizSession::class);

        /** @var User $targetUser */
        if ($targetUser = $userRepo->find($user_id)) {
            /** @var User $user */
            $user = $this->getUser();
            if ($targetUser === $user | $this->isGranted('ROLE_ADMIN')) {
                /** @var QuizSession $session */
                if ($session = $sessionRepo->getActiveSession($targetUser)) {
                    return $this->view($session);
                }

                return $this->createNotFoundException('No Active Session');
            }
        }

        return $this->createNotFoundException('Not owning user');
    }
}
