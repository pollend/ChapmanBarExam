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
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Illuminate\Support\Collection;

/**
 * @Route("/api/v1/")
 */
class QuizController extends AbstractFOSRestController
{

    /**
     * @Rest\Get("/quiz/{quiz_id}/questions/{page}",
     *     options = { "expose" = true },
     *     name="get_quiz_questions")
     * @Rest\View(serializerGroups={"detail","tags"})
     *
     * @param int $quiz_id
     * @param int $page
     * @return \FOS\RestBundle\View\View|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getQuestionsForPage($quiz_id,$page)
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
            if($activeSession->getCurrentPage() === $page)
                throw $this->createNotFoundException();

            $groups = $questionRepo->getUniqueGroups($quiz);
            $questions = $questionRepo->filterByGroup($groups[$page], $quiz);
            $groups = $questionRepo->getUniqueGroups($quiz);

            if ($activeSession->getQuiz() === $quiz | $this->isGranted(Grant::ROLE_ADMIN)) {
                return $this->view([
                    'questions' => $questions,
                    'max_pages' => Collection::make($groups)->keys()->max()
                ]);
            }
        }
        return $this->createNotFoundException();
    }
}