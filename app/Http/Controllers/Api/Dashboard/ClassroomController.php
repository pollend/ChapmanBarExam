<?php


namespace App\Http\Controllers\Api\Dashboard;


use App\Entities\Classroom;
use App\Entities\User;
use App\Http\Controllers\Controller;
use App\Repositories\ClassroomRepository;
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
            ->addDefaultListeners()
            ->build();
        $context = SerializationContext::create()->setGroups([
            'list',
            'timestamp'
        ]);

        return $serializer->serialize($classroomRepository->findAll(), 'json', $context);
    }
}