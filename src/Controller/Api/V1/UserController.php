<?php

namespace App\Controller\Api\V1;

use App\Entity\QuizSession;
use App\Entity\User;
use App\Repository\QuizSessionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/")
 */
class UserController extends AbstractController
{
    /**
     * @Route("user/{user_id}/quiz/session",
     *     options = { "expose" = true },
     *     name="get_user_quiz_session")
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
                    return $this->json($session,200,[],['groups' => ["detail", "meta"]]);
                }

                return $this->createNotFoundException('No Active Session');
            }
        }

        return $this->createNotFoundException('Not owning user');
    }
}
