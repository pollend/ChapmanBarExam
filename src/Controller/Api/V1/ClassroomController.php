<?php

namespace App\Controller\Api\V1;

use App\Entity\Classroom;
use App\Entity\QuizAccess;
use App\Entity\User;
use App\Form\ClassroomType;
use App\Form\QuizAccessType;
use App\Repository\ClassroomRepository;
use App\Repository\QuizAccessRepository;
use App\Repository\UserRepository;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/")
 */
class ClassroomController extends AbstractController
{
    /**
     * @Route("/classroom/owner/{user_id}", options = { "expose" = true }, name="get_classroom_by_owner",methods={"GET"})
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


                return $this->json(['classes' => $classrooms->toArray()],200,[],['groups'=>[
                    'list',
                    'classroom_access',
                    'access_quiz'
                ]]);
            }
        }

        throw $this->createNotFoundException();
    }

    /**
     * @Route("/classroom/datatable", options = { "expose" = true }, name="get_classrooms_datatable",methods={"POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function getClassroomDatatable(Request $request)
    {
        /** @var ClassroomRepository $classRepo */
        $classRepo = $this->getDoctrine()->getRepository(Classroom::class);

        return $this->json(['classes' => $classRepo->dataTable($request)],200,[],['groups'=> ["list","timestamp"]]);
    }

    /**
     * @Route("/classroom/{class_id}/access", options = { "expose" = true }, name="get_classroom_quiz_access",methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     */
    public function getQuizAccessForClassAction(Request $request, $class_id)
    {
        $em = $this->getDoctrine();
        /** @var ClassroomRepository $classRepository */
        $classRepository = $em->getRepository(Classroom::class);

        /** @var Classroom $class */
        if ($class = $classRepository->find($class_id)) {
            return $this->json(['quiz_access' => $class->getQuizAccess()],200,[],['groups'=>["list", "access_quiz","quiz"=>["list"]]]);
        }
        throw $this->createNotFoundException();
    }

    /**
     * @Route("/classroom/{class_id}/access/{access_id}", options = { "expose" = true }, name="patch_classroom_quiz_access",methods={"PATCH"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     *
     */
    public function patchQuizAccessAction(Request $request, $class_id, $access_id)
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
                $form->submit($request->request->all(), false);
                if ($form->isSubmitted() && $form->isValid()) {
                    $em->persist($quizAccess);
                    $em->flush();

                    return $this->json(['quiz_access' => $quizAccess],200,[],['groups'=>["list", "access_quiz","quiz"=>["list"]]]);
                }

                return $this->json($form);
            }
        }
        throw $this->createNotFoundException();
    }

    /**
     * @Route("/classroom/{class_id}", options = { "expose" = true }, name="get_classroom")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     */
    public function getClassroomAction($class_id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ClassroomRepository $classRepository */
        $classRepository = $em->getRepository(Classroom::class);

        /** @var Classroom $class */
        if ($class = $classRepository->find($class_id)) {
            return $this->json(['classroom' => $class],200,[],['groups'=>["list","timestamp"]]);
        }
        throw $this->createNotFoundException();
    }

    // tweak the role access with voter

    /**
     * @Route("/classroom/{class_id}", options = { "expose" = true }, name="patch_classroom",methods={"PATCH"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function patchClassroomAction(Request $request, $class_id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ClassroomRepository $classRepository */
        $classRepository = $em->getRepository(Classroom::class);
        /** @var Classroom $class */
        if ($class = $classRepository->find($class_id)) {
            $form = $this->createForm(ClassroomType::class, $class);
            $form->submit($request->request->all(), false);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($class);
                $em->flush();

                return $this->json(['classroom' => $class],200,[],['groups'=>["list","timestamp"]]);
            }

            return $this->json($form);
        }
        throw $this->createNotFoundException();
    }

//    /**
//     * @Post("/classroom/{class_id}/quiz/{quiz_id}/start", options = { "expose" = true }, name="post_classroom_quiz_start")
//     * @View(serializerGroups={"list"})
//     */
//    public function postStart($class_id, $quiz_id)
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        /** @var User $user */
//        $user = $this->getUser();
//
//        /** @var ClassroomRepository $classRepo */
//        $classRepo = $this->getDoctrine()->getRepository(Classroom::class);
//
//        /** @var QuizRepository $quizRepo */
//        $quizRepo = $this->getDoctrine()->getRepository(Quiz::class);
//
//        /** @var QuizAccessRepository $quizAccessRepo */
//        $quizAccessRepo = $this->getDoctrine()->getRepository(QuizAccess::class);
//
//        /** @var QuizSessionRepository $sessionRepo */
//        $sessionRepo = $this->getDoctrine()->getRepository(QuizSession::class);
//
//        /** @var Classroom $classroom */
//        if ($classroom = $classRepo->find($class_id)) {
//            /** @var Quiz $quiz */
//            if ($quiz = $quizRepo->find($quiz_id)) {
//                /* @var QuizAccess $access */
//                try {
//                    if ($access = $quizAccessRepo->getAccessByClassAndQuiz($classroom, $quiz)) {
//                        if (false == $classroom->isUserRegistered($user)) {
//                            throw $this->createNotFoundException('User Is Not Registered.');
//                        }
//                        if (null != $sessionRepo->getActiveSession($user)) {
//                            throw $this->createNotFoundException('Session Already Stareted.');
//                        }
//
//                        if ($access->isOpen($user)) {
//                            $session = new QuizSession();
//                            $session->setOwner($user)
//                                ->setQuiz($quiz)
//                                ->setClassroom($classroom);
//                            $em->persist($session);
//                            $em->flush();
//
//                            return $this->view(['session' => $session]);
//                        }
//                    }
//                } catch (NoResultException | NonUniqueResultException $e) {
//                    throw $this->createAccessDeniedException('Unknown access for class and quiz.');
//                }
//            }
//        }
//        throw $this->createNotFoundException();
//    }
}
