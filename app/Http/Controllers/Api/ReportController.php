<?php


namespace App\Http\Controllers\Api;


use App\Entities\MultipleChoiceQuestion;
use App\Entities\MultipleChoiceResponse;
use App\Entities\QuizQuestion;
use App\Entities\QuizSession;
use App\Entities\User;
use App\Http\Controllers\Controller;
use App\Repositories\QuestionRepository;
use App\Repositories\QuizSessionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
            'list',
            'timestamp'
        ]);
        return $serializer->serialize($sessions, 'json', $context);
    }

    /**
     *
     */
    public function meta(Request $request, $report){
        /** @var User $user */
        $user = Auth::user();
        /** @var QuizSessionRepository $repository */
        $repository = \EntityManager::getRepository(\App\Entities\QuizSession::class);

        /** @var QuestionRepository $questionRepository */
        $questionRepository = \EntityManager::getRepository(QuizQuestion::class);

        /** @var QuizSession $report */
        if($report = $repository->findOneBy(['id' => $report, 'owner' => $user])){
            $quiz = $report->getQuiz();
            return [
                'correct_count' => $report->calculateScore(),
                'non_response_count' => Collection::make($report->getNonResponseQuestions())->filter(function ($c){return $c instanceof MultipleChoiceQuestion;})->count(),
                'max_count' => $report->getMaxScore()];
        }
        abort(404);

    }

}