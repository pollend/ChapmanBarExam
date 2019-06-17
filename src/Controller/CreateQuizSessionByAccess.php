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
use App\Security\QuizSessionVoter;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class CreateQuizSessionByAccess
{

    private  $entityManager;
    private  $security;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager,Security $security, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->validator = $validator;
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
        $quizSessionRepository = $em->getRepository(QuizSession::class);

        $content = $request->getContent();
        if (empty($content)) {
            throw new BadRequestHttpException("Content is empty");
        }
        $contstraints = new Assert\Collection([
            'access_id' => [new Assert\NotBlank(), new Assert\Type("int")],
            'user_id' => [new Assert\NotBlank(), new Assert\Type("int")]
        ]);
        $data = json_decode($request->getContent(), true);
        $errors = $this->validator->validate($data, $contstraints);
        if (count($errors) > 0)
            return $errors;

        /** @var QuizAccess $access */
        if ($access = $quizAccessRepository->find($data['access_id'])) {
            if ($user === $userRepository->find($data['user_id'])) {
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
        throw new \Exception("Can't Start New Session");
    }

}