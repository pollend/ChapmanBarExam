<?php


namespace App\Controller;

use App\Entity\QuizQuestion;
use App\Entity\QuizSession;
use App\Repository\QuestionRepository;
use App\Repository\QuizSessionRepository;
use App\Security\QuizSessionVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Security\Core\Security;

class GetQuizQuestionBySessionAndPage
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

    public function __invoke(Request $request, $session_id, $page)
    {

        /** @var QuestionRepository $questionRepo */
        $questionRepo = $this->entityManager->getRepository(QuizQuestion::class);

        /** @var QuizSessionRepository $sessionRepo */
        $sessionRepo = $this->entityManager->getRepository(QuizSession::class);

        /** @var QuizSession $session */
        if ($session = $sessionRepo->find($session_id)) {
            $quiz = $session->getQuiz();
            if ($this->security->isGranted(QuizSessionVoter::VIEW, $session) && ($session->getSubmittedAt() !== null || $session->getCurrentPage() == $page)) {
                $groups = $questionRepo->getUniqueGroups($quiz);
                $questions = $questionRepo->filterByGroup($groups[$page], $quiz);
                $this->context->setParameter('session_id', $session_id);
                $this->context->setParameter('page', $page);
                return $questions;
            }

        }
        throw new \Exception("not found");
    }
}