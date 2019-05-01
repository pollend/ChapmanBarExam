<?php


namespace App\Http\Controllers;


use App\Exceptions\SessionInProgressException;
use App\Quiz;
use App\QuizSession;
use App\Repositories\QuizRepositoryInterface;
use App\Repositories\SessionRepositoryInterface;
use DebugBar\DebugBar;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SessionQuizController extends Controller
{

    private $sessionRepository;
    private $quizRepository;
    public function __construct(SessionRepositoryInterface $sessionRepository,QuizRepositoryInterface $quizRepository)
    {
        $this->sessionRepository = $sessionRepository;
        $this->quizRepository = $quizRepository;
    }

    public function startForm($id){
        return view('quiz.start',['quiz_id' => $id]);
    }

    public function start($id)
    {
        $quiz = Quiz::query()->where('id', $id)->firstOrFail();
        $user = Auth::user();
        $session = null;
        $session = $this->sessionRepository->startSession($user, $quiz);
        return redirect()->route('quiz.question', ['session_id' => $session->id, 'page' => 0]);
    }

    public function questionForm($session_id,$page){
        $user = Auth::user();
        $session = $this->sessionRepository->getActiveSession($user);
        if($session->id != $session_id){
            abort(404);
        }
        /** @var Quiz $quiz */
        $quiz = $session->quiz;
        $questions = $this->quizRepository->getQuestions($quiz);

        return view('quiz.question',[
            'questions' => $questions[$page],
            'page' => $page,
            'maxPage' => $questions->keys()->max(),
            'session_id'=> $session_id
        ]);

    }

    public function question($session_id){

    }

}