<?php


namespace App\Http\Controllers\Api;


use App\Entities\User;
use App\Http\Controllers\Controller;
use App\Repositories\QuizSessionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
class ReportController extends Controller
{

    public function __construct()
    {
    }

    /**
     * TODO: work on queries for sorting
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        /** @var QuizSessionRepository $repository */
        $repository = \EntityManager::getRepository(\App\Entities\QuizSession::class);

        $sessions = $repository->getSessionsByUser($user);

        $serializer = SerializerBuilder::create()->configureListeners(function (EventDispatcher $dispatcher) {
            $dispatcher->addListener('serializer.post_serialize', function (ObjectEvent $event) {
                $event->getVisitor()->setData('uri', route('report.show', ['report' => $event->getObject()->getId()]));

            }, 'App\Entities\QuizSession');
        })->addDefaultListeners()
            ->build();

        $context = SerializationContext::create()->setGroups([
            'list'
        ]);
        return $serializer->serialize($sessions, 'json', $context);
    }

    /**
     *
     */
    public function getQuizScores(Request $request){
        /** @var User $user */
        $user = Auth::user();
        /** @var QuizSessionRepository $repository */
        $repository = \EntityManager::getRepository(\App\Entities\QuizSession::class);


    }

}