<?php
namespace App\Controller\Api\V1;


use App\Entity\Classroom;
use App\Entity\Quiz;
use App\Entity\QuizAccess;
use App\Entity\QuizSession;
use App\Entity\User;
use App\Form\ClassroomType;
use App\Form\QuizAccessType;
use App\Repository\ClassroomRepository;
use App\Repository\QuizAccessRepository;
use App\Repository\QuizRepository;
use App\Repository\QuizSessionRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\View;
use HttpException;
use Illuminate\Support\Collection;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\Expression\Expression;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/v1/")
 */
class ClassroomController extends AbstractFOSRestController
{
    /**
     * @Get("/classroom/owner/{user_id}", options = { "expose" = true }, name="get_classroom_by_owner")
     */
    public function getClassroomsByOwner($user_id)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()->getRepository(User::class);

        /** @var User $user */
        $user = $this->getUser();

        $builder = SerializerBuilder::create();
        /** @var User $targetUser */
        if ($targetUser = $userRepository->find($user_id)) {
            if ($user === $targetUser | $this->isGranted('ROLE_ADMIN')) {

                $builder->configureListeners(function (EventDispatcher $dispatcher) use ($targetUser) {
                    $dispatcher->addListener('serializer.post_serialize', function (ObjectEvent $event) use ($targetUser) {
                        /** @var QuizAccess $access */
                        $access = $event->getObject();
                        $event->getVisitor()->setData('user_attempts', $access->getQuiz()->getQuizSessionsByUser($targetUser)->count());
                    }, 'App\Entity\QuizAccess');
                });

                $serializer = $builder->build();
                /** @var ClassroomRepository $classroomRepository */
                $classroomRepository = $this->getDoctrine()->getRepository(Classroom::class);
                $classrooms = $classroomRepository->byUser($targetUser);

                $context = SerializationContext::create()->setGroups([
                    'list',
                    'classroom_access',
                    'quizAccess' => [
                        'list',
                        'access_quiz',
                        'quiz' => [
                            'list'
                        ]
                    ]
                ]);
                return $this->view($serializer->toArray(['classes' => $classrooms->toArray()], $context));
            }
        }

        throw $this->createNotFoundException();
    }

    /**
     * @Post("/classroom/datatable", options = { "expose" = true }, name="get_classrooms_datatable")
     * @View(serializerGroups={"Default","list","timestamp"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function getClassroomDatatable(Request $request){
        /** @var ClassroomRepository $classRepo */
        $classRepo = $this->getDoctrine()->getRepository(Classroom::class);
        return $this->view(['classes' => $classRepo->dataTable($request)]);
    }

    /**
     * @Get("/classroom/{class_id}/access", options = { "expose" = true }, name="get_classroom_quiz_access")
     * @View(serializerGroups={"list", "access_quiz","quiz":{"list"}})
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     */
    public function getQuizAccessForClassAction(Request $request,$class_id)
    {
        $em = $this->getDoctrine();
        /** @var ClassroomRepository $classRepository */
        $classRepository = $em->getRepository(Classroom::class);

        /** @var Classroom $class */
        if ($class = $classRepository->find($class_id)) {
            return $this->view(['quiz_access' => $class->getQuizAccess()]);
        }
        throw $this->createNotFoundException();
    }

    /**
     * @Patch("/classroom/{class_id}/access/{access_id}", options = { "expose" = true }, name="patch_classroom_quiz_access")
     * @Security("has_role('ROLE_ADMIN')")
     * @View(serializerGroups={"list", "access_quiz","quiz":{"list"}})
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     */
    public function patchQuizAccessAction(Request $request,$class_id,$access_id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ClassroomRepository $classRepository */
        $classRepository = $em->getRepository(Classroom::class);

        /** @var QuizAccessRepository $quizAccessRepository */
        $quizAccessRepository = $em->getRepository(QuizAccess::class);

        /** @var Classroom $class */
        if ($class = $classRepository->find($class_id)) {
            /** @var QuizAccess $quizAccess */
            if ($quizAccess = $quizAccessRepository->findOneBy(['id' => $access_id, 'classroom' => $class])) {
                $form = $this->createForm(QuizAccessType::class, $quizAccess);
                $form->submit($request->request->all(),false);
                if ($form->isSubmitted() && $form->isValid()) {
                    $em->persist($quizAccess);
                    $em->flush();
                    return $this->view(['quiz_access' => $quizAccess]);
                }
                return $this->view($form);
            }
        }
        throw $this->createNotFoundException();
    }


    /**
     * @Get("/classroom/{class_id}", options = { "expose" = true }, name="get_classroom")
     * @View(serializerGroups={"list","timestamp"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     */
    public function getClassroomAction($class_id){
        $em = $this->getDoctrine()->getManager();

        /** @var ClassroomRepository $classRepository */
        $classRepository = $em->getRepository(Classroom::class);

        /** @var Classroom $class */
        if ($class = $classRepository->find($class_id)) {
            return $this->view(['classroom' => $class]);
        }
        throw $this->createNotFoundException();
    }

    // tweak the role access with voter
    /**
     * @Patch("/classroom/{class_id}", options = { "expose" = true }, name="patch_classroom")
     * @View(serializerGroups={"list","timestamp"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function patchClassroomAction(Request $request,$class_id){
        $em = $this->getDoctrine()->getManager();

        /** @var ClassroomRepository $classRepository */
        $classRepository = $em->getRepository(Classroom::class);
        /** @var Classroom $class */
        if($class = $classRepository->find($class_id)){
            $form  = $this->createForm(ClassroomType::class, $class);
            $form->submit($request->request->all(),false);
            if($form->isSubmitted() && $form->isValid()){
                $em->persist($class);
                $em->flush();
                return $this->view(['classroom' => $class]);
            }
            return $this->view($form);
        }
        throw $this->createNotFoundException();
    }


    /**
     * @Post("/classroom/{class_id}/quiz/{quiz_id}/start", options = { "expose" = true }, name="post_classroom_quiz_start")
     * @View(serializerGroups={"list"})
     */
    public function postStart($class_id, $quiz_id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $this->getUser();

        /** @var ClassroomRepository $classRepo */
        $classRepo = $this->getDoctrine()->getRepository(Classroom::class);

        /** @var QuizRepository $quizRepo */
        $quizRepo = $this->getDoctrine()->getRepository(Quiz::class);

        /** @var QuizAccessRepository $quizAccessRepo */
        $quizAccessRepo = $this->getDoctrine()->getRepository(QuizAccess::class);

        /** @var QuizSessionRepository $sessionRepo */
        $sessionRepo = $this->getDoctrine()->getRepository(QuizSession::class);

        /** @var Classroom $classroom */
        if ($classroom = $classRepo->find($class_id)) {
            /** @var Quiz $quiz */
            if ($quiz = $quizRepo->find($quiz_id)) {
                /** @var QuizAccess $access */
                try {
                    if ($access = $quizAccessRepo->getAccessByClassAndQuiz($classroom, $quiz)) {
                        if ($classroom->isUserRegistered($user) == false) {
                            throw $this->createNotFoundException("User Is Not Registered.");
                        }
                        if ($sessionRepo->getActiveSession($user) != null)
                            throw $this->createNotFoundException("Session Already Stareted.");

                        if ($access->isOpen($user)) {
                            $session = new QuizSession();
                            $session->setOwner($user)
                                ->setQuiz($quiz)
                                ->setClassroom($classroom);
                            $em->persist($session);
                            $em->flush();
                            return $this->view(['session' => $session]);
                        }
                    }
                } catch (NoResultException | NonUniqueResultException $e) {
                    throw $this->createAccessDeniedException("Unknown access for class and quiz.");
                }
            }
        }
        throw $this->createNotFoundException();
    }
}