<?php


namespace App\Http\Controllers;


use App\Quiz;
use App\QuizMultipleChoiceQuestion;
use App\QuizMultipleChoiceResponse;
use App\QuizSession;
use App\Repositories\QuizRepositoryInterface;
use App\Repositories\SessionRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    private $quizRepository;
    private $sessionRepository;

    public function __construct(QuizRepositoryInterface $quizRepository,SessionRepositoryInterface $sessionRepository)
    {
        $this->quizRepository = $quizRepository;
        $this->sessionRepository = $sessionRepository;
    }

    public function index()
    {
        return view('report.index');
    }

    public function show($report)
    {
        $user = Auth::user();

        $session = QuizSession::query()
            ->where('owner_id',$user->id)
            ->where('id',$report)
            ->first();

//        $quiz = $session->quiz();

        \Debugbar::info(QuizMultipleChoiceResponse::query()->select('*')->bySession($session)->correctEntry()->get());

        $questions = $this->quizRepository->getUnionedQuestions(function ($query) use ($session){
           return $query->where('quiz_id',$session->quiz_id);
        });

        \Debugbar::info($questions->get());

        return view('report.show');
    }
}