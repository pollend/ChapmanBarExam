<?php


namespace App\Controller;


use App\Entity\Quiz;
use App\Entity\QuizAccess;
use App\Entity\QuizSession;
use App\Entity\User;
use App\Repository\QuizAccessRepository;
use App\Repository\QuizSessionRepository;
use App\Repository\UserRepository;
use App\Security\QuizAccessVoter;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class CreateQuizSessionByAccess
{

    private  $entityManager;
    private  $security;
    public function __construct(EntityManagerInterface $entityManager,Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function __invoke(Request $request)
    {
        $em = $this->entityManager;
        /** @var User $user */
        $user = $this->security->getUser();

        /** @var QuizAccessRepository $quizAccessRepository */
        $quizAccessRepository = $em->getRepository(QuizAccess::class);

        /** @var UserRepository $userRepository */
        $userRepository = $em->getRepository(User::class);

        /** @var QuizSessionRepository $quizSessionRepository */
        $quizSessionRepository  = $em->getRepository(QuizSession::class);

        /** @var QuizAccess $access */
        if($access = $quizAccessRepository->find($request->get('access_id',null)))
        {
            if($user === $userRepository->find($request->get('user_id',null))) {
                if ($this->security->isGranted(QuizAccessVoter::START, $access)) {

                    if (Collection::make($quizSessionRepository->getActiveSessions($user))->count() > 0) {
                        throw new \Exception("Session already Started");
                    }

                    $session = new QuizSession();
                    $session->setOwner($user)
                        ->setClassroom($access->getClassroom())
                        ->setQuiz($access->getQuiz());
                    $session->setMeta([]);
                    $em->persist($session);
                    $em->flush();
                    return $session;
                }
            }
        }
        throw new \Exception("Can't Start New Session");
    }

}