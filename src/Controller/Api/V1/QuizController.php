<?php

namespace App\Controller\Api\V1;

use App\Entity\Quiz;
use App\Entity\QuizQuestion;
use App\Entity\QuizSession;
use App\Entity\User;
use App\Grant;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use App\Repository\QuizSessionRepository;
use Illuminate\Support\Collection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/")
 */
class QuizController extends AbstractController
{
    /**
     * @Route("/quiz/{quiz_id}/questions/{page}",
     *     options = { "expose" = true },
     *     name="get_quiz_questions",methods={"GET"})
     *
     * @param int $quiz_id
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getQuestionsForPage($quiz_id, $page)
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var QuestionRepository $questionRepo */
        $questionRepo = $this->getDoctrine()->getRepository(QuizQuestion::class);

        /** @var QuizSessionRepository $quizSessionRepo */
        $quizSessionRepo = $this->getDoctrine()->getRepository(QuizSession::class);

        /** @var QuizRepository $quizRepo */
        $quizRepo = $this->getDoctrine()->getRepository(Quiz::class);

        /** @var Quiz $quiz */
        if ($quiz = $quizRepo->find($quiz_id)) {
            /** @var QuizSession $activeSession */
            $activeSession = $quizSessionRepo->getActiveSession($user);
            if ($activeSession->getCurrentPage() === $page) {
                throw $this->createNotFoundException();
            }

            $groups = $questionRepo->getUniqueGroups($quiz);
            $questions = $questionRepo->filterByGroup($groups[$page], $quiz);
            $groups = $questionRepo->getUniqueGroups($quiz);

            if ($activeSession->getQuiz() === $quiz | $this->isGranted(Grant::ROLE_ADMIN)) {
                return $this->json([
                    'questions' => $questions,
                    'max_pages' => Collection::make($groups)->keys()->max(),
                ], 200, [], ['groups' => ["detail", "tags"]]);
            }
        }

        return $this->createNotFoundException();
    }

    /**
     * @Route("/quiz/datatable",
     *     options = { "expose" = true },
     *     name="get_quiz_datatable",methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function getQuizzesDatatable(Request $request)
    {
        /** @var QuizRepository $quizRepo */
        $quizRepo = $this->getDoctrine()->getRepository(Quiz::class);

        return $this->json(['quizzes' => $quizRepo->dataTable($request)],200,[],['groups' => ["Default","list","timestamp"]]);
    }
}
