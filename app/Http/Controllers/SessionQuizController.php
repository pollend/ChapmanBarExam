<?php


namespace App\Http\Controllers;


use App\Exceptions\SessionInProgressException;
use App\Quiz;
use App\QuizSession;
use App\Repositories\SessionRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SessionQuizController extends Controller
{

    private $sessionRepository;

    public function __construct(SessionRepositoryInterface $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public function startForm($id){
        return view('quiz.start',['quiz_id' => $id]);
    }

    public function start($id)
    {
        if (Auth::check()) {
            $quiz = Quiz::where('id', $id)->firstOrFail();
            $user = Auth::user();
            $session = null;
            $session = $this->sessionRepository->startSession($user, $quiz);
            return redirect()->route('quiz.question', ['session_id' => $session->id, 'page' => 0]);
        }
        throw new AuthorizationException();
    }


    public function questionForm($session_id,$page){

    }

    public function question($session_id){

    }

}