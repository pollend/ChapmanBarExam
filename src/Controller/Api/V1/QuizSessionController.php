<?php


namespace App\Controller\Api\V1;


use App\Entity\QuizQuestion;
use App\Entity\QuizResponse;
use App\Entity\QuizSession;
use App\Entity\User;
use App\Repository\QuestionRepository;
use App\Repository\QuizResponseRepository;
use App\Repository\QuizSessionRepository;
use Doctrine\Common\Collections\Criteria;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Illuminate\Support\Collection;

use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Route("/api/v1/")
 */
class QuizSessionController extends AbstractFOSRestController
{

    /**
     * @Rest\Get("/quiz/session/{session_id}/page/{page}",
     *     options = { "expose" = true },
     *     name="get_quiz_session_questions")
     * @Rest\View(serializerGroups={"detail","tags","correct"})
     */
    public function getSessionQuestionPage($session_id,$page)
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var QuizSessionRepository $sessionRepo */
        $sessionRepo = $this->getDoctrine()->getRepository(QuizSession::class);

        /** @var QuestionRepository $questionRepo */
        $questionRepo = $this->getDoctrine()->getRepository(QuizQuestion::class);

        /** @var QuizResponseRepository $quizResponseRepo */
        $quizResponseRepo = $this->getDoctrine()->getRepository(QuizResponse::class);


        /** @var QuizSession $session */
        if ($session = $sessionRepo->find($session_id)) {
            if ($session->getOwner() === $user | $this->isGranted('ROLE_ADMIN')) {
                $quiz = $session->getQuiz();

                $groups = $questionRepo->getUniqueGroups($quiz);
                $questions = $questionRepo->filterByGroup($groups[$page], $quiz);
                $responses = $quizResponseRepo->filterResponsesBySessionAndQuestions($session,$questions);

                return $this->view([
                    'page' => $page,
                    'session' => $session,
                    'responses' => $responses->toArray(),
                    'maxPage' => Collection::make($groups)->keys()->max()
                ]);
            }
        }
        throw $this->createNotFoundException();
    }

    /**
     * @Rest\Get("/quiz/session/active",
     *     options = { "expose" = true },
     *     name="get_active_session")
     * @Rest\View(serializerGroups={"classroom","detail","quiz","quiz":{"list"}})
     */
    public function getActiveSession()
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var QuizSessionRepository $sessionRepo */
        $sessionRepo = $this->getDoctrine()->getRepository(QuizSession::class);

        /** @var QuizSession $session */
        if ($session = $sessionRepo->getActiveSession($user)) {
            return $this->view(['session' => $session]);
        }
        throw $this->createNotFoundException("No Active Session");
    }

    /**
     * @Rest\Get("/quiz/session/{session_id<\d+>}",
     *     options = { "expose" = true },
     *     name="get_session")
     * @Rest\View(serializerGroups={"classroom","detail","quiz","quiz":{"list"}})
     */
    public function postSessionQuestionPage($session_id)
    {
        /** @var QuizSessionRepository $sessionRepo */
        $sessionRepo = $this->getDoctrine()->getRepository(QuizSession::class);
        if ($session = $sessionRepo->find($session_id)) {
            return $this->view(['session' => $session]);
        }
        throw  $this->createNotFoundException();
    }


}