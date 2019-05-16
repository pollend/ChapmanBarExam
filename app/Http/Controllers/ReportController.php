<?php


namespace App\Http\Controllers;

use App\Entities\QuizQuestion;
use App\Entities\QuizSession;
use App\Repositories\QuestionRepository;
use App\Repositories\QuizSessionRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;

class ReportController extends Controller
{

    public function __construct()
    {
    }

    public function index(Request $request)
    {

        return view('report.index');
    }

    public function show(Request $request,$report)
    {
        /** @var QuizSessionRepository $quizSessionRepository */
        $quizSessionRepository = \EntityManager::getRepository(QuizSession::class);
        /** @var QuestionRepository $questionRepository */
        $questionRepository = \EntityManager::getRepository(QuizQuestion::class);

        $user = \Auth::user();
        /** @var QuizSession $report */
        if ($report = $quizSessionRepository->findOneBy(['id' => $report,'owner' => $user])) {
            \Debugbar::info($report);

            $groups = $questionRepository->getUniqueGroups($report->getQuiz());
            $questionGroups = Collection::make();
            foreach ($groups as $group){
                $questionGroups->add($questionRepository->filterByGroup($group, $report->getQuiz()));
            }

            return view('report.show', ['quizSession' => $report, 'questions' => $questionGroups]);
        }
        abort(404);
        return null;
    }
}