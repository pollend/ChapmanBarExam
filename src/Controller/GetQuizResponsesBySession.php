<?php


namespace App\Controller;


use App\Entity\QuizResponse;
use App\Entity\QuizSession;
use App\Repository\QuizResponseRepository;
use App\Repository\QuizSessionRepository;
use App\Security\QuizSessionVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Security\Core\Security;

class GetQuizResponsesBySession
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

    public function __invoke($session_id)
    {
        /** @var QuizResponseRepository $quizResponseRepository */
        $quizResponseRepository = $this->entityManager->getRepository(QuizResponse::class);

        /** @var QuizSessionRepository $quizSessionRepository*/
        $quizSessionRepository = $this->entityManager->getRepository(QuizSession::class);

        if($session = $quizSessionRepository->find($session_id)){
            if($this->security->isGranted(QuizSessionVoter::VIEW,$session)) {
                $responses = $quizResponseRepository->bySession($session);
                $this->context->setParameter('session_id', $session_id);
                return $responses;
            }
        }
        throw new \Exception('unknown session');
    }
}