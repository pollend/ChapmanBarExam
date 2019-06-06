<?php
namespace App\Controller\Api\V1;

use App\Entity\QuizSession;
use App\Entity\User;
use App\Grant;
use App\Repository\QuizSessionRepository;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;

use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Route("/api/v1/")
 */
class ReportController extends AbstractFOSRestController
{

    /**
     * @Rest\Get("/report/{user_id}",
     *     options = { "expose" = true },
     *     name="get_reports")
     * @Rest\View(serializerGroups={"list","timestamp","results", "quiz", "quiz":{"list"}})
     */
    public function getReports($user_id){
        /** @var User $user */
        $user = $this->getUser();

        /** @var UserRepository $userRepo */
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        /** @var QuizSessionRepository $quizSessionRepo */
        $quizSessionRepo = $this->getDoctrine()->getRepository(QuizSession::class);
        if($targetUser = $userRepo->find($user_id)){
            if($user === $targetUser | $this->isGranted(Grant::ROLE_ADMIN)){
                $sessions = $quizSessionRepo->getSessionsByUser($user);
                return $this->view(['reports' => $sessions]);
            }
        }
        throw $this->createNotFoundException();
    }
}