<?php


namespace App\Http\Controllers\Api\Dashboard;


use App\Entities\Classroom;
use App\Entities\User;
use App\Http\Controllers\Controller;
use App\Repositories\ClassroomRepository;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;

class ClassroomController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = \Auth::user();

        /** @var ClassroomRepository $classroomRepository */
        $classroomRepository = \EntityManager::getRepository(Classroom::class);

        $serializer = SerializerBuilder::create()
            ->configureListeners(function (EventDispatcher $dispatcher){
                $dispatcher->addListener('serializer.post_serialize', function (ObjectEvent $event) {
                    $event->getVisitor()->setData('uri', route('report.show', ['report' => $event->getObject()->getId()]));
                }, 'App\Entities\Classroom');
            })
            ->addDefaultListeners()
            ->build();
        $context = SerializationContext::create()->setGroups([
            'list',
            'timestamp'
        ]);

        return $serializer->serialize($classroomRepository->findAll(), 'json', $context);
    }
}