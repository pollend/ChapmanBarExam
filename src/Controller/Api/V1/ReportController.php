<?php

namespace App\Controller\Api\V1;

use App\Entity\QuestionTag;
use App\Entity\QuizQuestion;
use App\Entity\QuizResponse;
use App\Entity\QuizSession;
use App\Entity\User;
use App\Event\QuestionResultsEvent;
use App\Grant;
use App\Repository\QuestionRepository;
use App\Repository\QuestionTagRepository;
use App\Repository\QuizResponseRepository;
use App\Repository\QuizSessionRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Criteria;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @Route("/api/v1/")
 */
class ReportController extends AbstractFOSRestController
{
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @Rest\Get("/report/user/{user_id}",
     *     options = { "expose" = true },
     *     name="get_reports_by_user")
     * @Rest\View(serializerGroups={"list","timestamp","results", "quiz", "quiz":{"list"}})
     */
    public function getReportsByUser($user_id)
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var UserRepository $userRepo */
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        /** @var QuizSessionRepository $quizSessionRepo */
        $quizSessionRepo = $this->getDoctrine()->getRepository(QuizSession::class);
        if ($targetUser = $userRepo->find($user_id)) {
            if ($user === $targetUser | $this->isGranted(Grant::ROLE_ADMIN)) {
                $sessions = $quizSessionRepo->getSessionsByUser($user);

                return $this->view(['reports' => $sessions]);
            }
        }
        throw $this->createNotFoundException();
    }

    /**
     * @Rest\Get("/report",
     *     options = { "expose" = true },
     *     name="get_reports")
     * @Rest\View(serializerGroups={"list","timestamp","results", "quiz", "quiz":{"list"}})
     */
    public function getReports(Request $request)
    {
    }

    /**
     * @Rest\Get("/report/{report_id}",
     *     options = { "expose" = true },
     *     name="get_report")
     * @Rest\View(serializerGroups={"detail","question_tags","question":{},"user_response","question_correct_answer"})
     */
    public function getReport($report_id)
    {
        /** @var QuizSessionRepository $quizSessionRepository */
        $quizSessionRepository = $this->getDoctrine()->getRepository(QuizSession::class);
        /** @var QuizResponseRepository $quizResponseRepository */
        $quizResponseRepository = $this->getDoctrine()->getRepository(QuizResponse::class);

        $user = $this->getUser();
        /** @var QuizSession $session */
        if ($session = $quizSessionRepository->findOneBy(['id' => $report_id, 'owner' => $user])) {
            $responses = Collection::make($quizResponseRepository->filterResponsesBySession($session))->keyBy(function ($response) {
                return $response->getQuestion()->getId();
            });
            $questions = Collection::make($session->getQuiz()->getQuestions()->matching(Criteria::create()->orderBy(['group' => 'ASC', 'order' => 'ASC'])));

            return $this->view(['questions' => $questions->toArray(), 'responses' => $responses->toArray()]);
        }
        throw $this->createNotFoundException();
    }

    /**
     * @Rest\Get("/report/{report_id}/breakdown",
     *     options = { "expose" = true },
     *     name="get_report_breakdown")
     * @Rest\View(serializerGroups={"detail"})
     */
    public function getReportBreakdown($report_id)
    {
        /** @var QuizSessionRepository $quizSessionRepository */
        $quizSessionRepository = $this->getDoctrine()->getRepository(QuizSession::class);
        /** @var QuizResponseRepository $quizResponseRepository */
        $quizResponseRepository = $this->getDoctrine()->getRepository(QuizResponse::class);
        /** @var QuestionTagRepository $questionTagRepository */
        $questionTagRepository = $this->getDoctrine()->getRepository(QuestionTag::class);
        /** @var QuestionRepository $questionRepository */
        $questionRepository = $this->getDoctrine()->getRepository(QuizQuestion::class);

        $user = $this->getUser();

        /** @var QuizSession $session */
        if ($session = $quizSessionRepository->findOneBy(['id' => $report_id, 'owner' => $user])) {
            $tags = Collection::make($questionTagRepository->getUniqueTagsForQuiz($session->getQuiz()))->keyBy(function ($tag) {
                return $tag->getId();
            });
            $breakdown = Collection::make();
            /** @var QuestionTag $tag */
            foreach ($tags as $tag) {
                $questionsByTag = $questionRepository->filterByTag($session->getQuiz(), $tag);
                $event = new QuestionResultsEvent($session, $questionsByTag);
                $this->dispatcher->dispatch($event, QuestionResultsEvent::QUESTION_RESULTS);
                $breakdown->put($tag->getId(), $event);
            }

            return $this->view(['tags' => $tags->toArray(), 'breakdown' => $breakdown->toArray()]);
        }
        throw $this->createNotFoundException();
    }
}
