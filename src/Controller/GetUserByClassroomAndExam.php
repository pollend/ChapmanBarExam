<?php


namespace App\Controller;


use App\Entity\Classroom;
use App\Entity\Quiz;
use App\Entity\QuizSession;
use App\Entity\User;
use App\Repository\ClassroomRepository;
use App\Repository\QuizRepository;
use App\Repository\QuizSessionRepository;
use App\Repository\UserRepository;
use App\utility\UserSessionPayload;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Security\Core\Security;

class GetUserByClassroomAndExam
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


    public function __invoke(Request $request, $class_id, $exam_id)
    {
        $this->context->setParameter('class_id', $class_id);
        $this->context->setParameter('exam_id', $exam_id);

        /** @var UserRepository $userRepo */
        $userRepo = $this->entityManager->getRepository(User::class);

        /** @var QuizRepository $quizRepo */
        $quizRepo = $this->entityManager->getRepository(Quiz::class);

        /** @var QuizSessionRepository $quizSessionRepo */
        $quizSessionRepo = $this->entityManager->getRepository(QuizSession::class);

        /** @var ClassroomRepository $classRepo */
        $classRepo = $this->entityManager->getRepository(Classroom::class);

        /** @var Classroom $class */
        if($classroom = $classRepo->find($class_id)){
            /** @var Quiz $exam */
            if($exam = $quizRepo->find($exam_id)){
                $results = Collection::make();
                /** @var User $user */
                $userRepo->filterByClassAndExam($classroom,$exam);
                foreach ($userRepo->filterByClassAndExam($classroom,$exam) as $user){
                    $results->push(new UserSessionPayload($user, $quizSessionRepo->getSessionsByClassAndQuizAndUser($exam,$classroom,$user)));
                }
                return $results;
            }
        }
        throw new \Exception("not found");

    }
}
