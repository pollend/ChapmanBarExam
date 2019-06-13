<?php

namespace App\Controller\Api\V1;

use App\Entity\MultipleChoiceEntry;
use App\Entity\MultipleChoiceQuestion;
use App\Entity\MultipleChoiceResponse;
use App\Entity\Quiz;
use App\Entity\QuizQuestion;
use App\Entity\QuizResponse;
use App\Entity\QuizSession;
use App\Entity\User;
use App\Repository\MultipleChoiceEntryRepository;
use App\Repository\QuestionRepository;
use App\Repository\QuizResponseRepository;
use App\Repository\QuizSessionRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/")
 */
class QuizSessionController extends AbstractController
{
    /**
     * @Route("/quiz/session/{session_id}/page/{page}",
     *     options = { "expose" = true },
     *     name="get_quiz_session_questions", methods={"GET"})
     */
    public function getSessionQuestionPage($session_id, $page)
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
                $responses = $quizResponseRepo->filterResponsesBySessionAndQuestions($session, $questions);

                return $this->json([
                    'page' => $page,
                    'session' => $session,
                    'responses' => $responses,
                    'maxPage' => Collection::make($groups)->keys()->max(),
                ], 200, [], ['ignored_attributes' => ['quizSessions'], 'groups' => ["detail", "tags", "correct"]]);
            }
        }
        throw $this->createNotFoundException();
    }

    /**
     * @Route("/quiz/session/{session_id}/page/{page}",
     *     options = { "expose" = true },
     *     name="post_quiz_session_questions", methods={"POST"})
     */
    public function postSessionQuestionPage(Request $request, $session_id, $page)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var QuestionRepository $questionRepository */
        $questionRepository = $this->getDoctrine()->getRepository(QuizQuestion::class);

        /** @var QuizSession $session */
        if ($session = $this->checkActiveSession($session_id)) {
            //make sure the pages match when submitting
            if ($session->getCurrentPage() !== (int)$page) {
                throw $this->createNotFoundException();
            }

            if ($this->submitQuestions($request, $session, $page)) {
                /** @var Quiz $quiz */
                $quiz = $session->getQuiz();
                $groups = Collection::make($questionRepository->getUniqueGroups($quiz));

                $session->setCurrentPage($session->getCurrentPage() + 1);
                if ($session->getCurrentPage() > $groups->keys()->max()) {
                    $session->setSubmittedAt(Carbon::now());
                }
                $em->persist($session);
                $em->flush();

                return $this->json($this->getSessionQuestionPage($session_id, $page), 200, [], ['groups' => ["detail", "tags", "correct"]]);
            }
        }
        throw $this->createNotFoundException();
    }

    /**
     * @Route("/quiz/session/{session_id}/page/{page}",
     *     options = { "expose" = true },
     *     name="patch_quiz_session_questions", methods={"PATCH"})
     */
    public function patchSessionQuestionPage(Request $request, $session_id, $page)
    {
        if ($session = $this->checkActiveSession($session_id)) {
            //make sure the pages match when submitting
            if ($session->getCurrentPage() !== (int)$page) {
                throw $this->createNotFoundException();
            }

            if ($this->submitQuestions($request, $session, $page)) {
                return $this->getSessionQuestionPage($session_id, $page);
            }
        }

        throw $this->createNotFoundException();
    }

    private function checkActiveSession($session_id) : QuizSession
    {
        $user = $this->getUser();
        /** @var QuizSessionRepository $sessionRepository */
        $sessionRepository = $this->getDoctrine()->getRepository(QuizSession::class);
        /** @var QuizSession $session */
        if ($session = $sessionRepository->getActiveSession($user)) {
            if ($session->getId() === (int)$session_id)
                return $session;
        }
        return null;
    }

    /**
     * @param Request $request
     * @param QuizSession $session
     * @param $page
     *
     * @return bool
     */
    private function submitQuestions(Request $request, $session, $page)
    {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        /** @var QuizSessionRepository $sessionRepository */
        $sessionRepository = $this->getDoctrine()->getRepository(QuizSession::class);
        /** @var QuestionRepository $questionRepository */
        $questionRepository = $this->getDoctrine()->getRepository(QuizQuestion::class);
        /** @var QuizResponseRepository $responseRepository */
        $responseRepository = $this->getDoctrine()->getRepository(QuizResponse::class);
        /** @var MultipleChoiceEntryRepository $multipleChoiceEntryRepository */
        $multipleChoiceEntryRepository = $this->getDoctrine()->getRepository(MultipleChoiceEntry::class);

        /** @var Quiz $quiz */
        $quiz = $session->getQuiz();
        $groups = $questionRepository->getUniqueGroups($quiz);
        $group = $groups[$page];

        $questions = Collection::make($questionRepository->filterByGroup($group, $quiz))->keyBy(function ($q) {
            return $q->getId();
        });
        if ($responses = $request->get('responses')) {
            foreach ($responses as $key => $target) {
                /** @var QuizQuestion $question */
                if ($question = $questions[$key]) {
                    $response = $responseRepository->filterResponseBySessionAndQuestion($session, $question);
                    if ($question instanceof MultipleChoiceQuestion) {
                        if (null == $response) {
                            $response = new MultipleChoiceResponse();
                        }
                        /** @var MultipleChoiceEntry $entry */
                        foreach ($multipleChoiceEntryRepository->getEntriesForQuestion($question) as $entry) {
                            if ($entry->getId() == $target) {
                                $response->setChoice($entry);
                            }
                        }
                    }
                    $response->setQuestion($question);
                    $response->setSession($session);
                    $em->persist($response);
                }
            }
        }

        $em->flush();

        return true;
    }

    /**
     * @Route("/quiz/session/active",
     *     options = { "expose" = true },
     *     name="get_active_session", methods={"GET"})
     */
    public function getActiveSession()
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var QuizSessionRepository $sessionRepo */
        $sessionRepo = $this->getDoctrine()->getRepository(QuizSession::class);

        /** @var QuizSession $session */
        if ($session = $sessionRepo->getActiveSession($user)) {
            return $this->json(['session' => $session], 200, [], ['ignored_attributes' => ['questions', 'quizSessions'], 'groups' => ["session_classroom", "detail", "quiz"]]);
        }
        throw $this->createNotFoundException('No Active Session');
    }

    /**
     * @Route("/quiz/session/{session_id<\d+>}",
     *     options = { "expose" = true },
     *     name="get_session", methods={"GET"})
     */
    public function getSessionById($session_id)
    {
        /** @var QuizSessionRepository $sessionRepo */
        $sessionRepo = $this->getDoctrine()->getRepository(QuizSession::class);
        if ($session = $sessionRepo->find($session_id)) {
            return $this->json(['session' => $session], 200, [], ["classroom", "detail", "quiz", "quiz" => ["list"]]);
        }
        throw  $this->createNotFoundException();
    }
}
