<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiReportController  extends Controller
{
    private $sessionRepository;

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
//        $user = Auth::user();
//
//        $sessions = QuizSession::query()
//            ->select('quiz_sessions.*','quizzes.name as quiz.name','quizzes.time_open as quiz.time_open')
//            ->byOwner($user)
//            ->join('quizzes','quizzes.id', '=','quiz_sessions.quiz_id')
//            ->orderBy("quizzes.name","DESC")
//            ->get();
//        $quizzes = Quiz::query()
//            ->whereIn('id',$sessions->groupBy('quiz_id')->keys())
//            ->get()
//            ->keyBy('id');
//        return $sessions->map(function ($item, $key) use ($quizzes){
//            $item->quiz = $quizzes[$item['quiz_id']];
//            $item->max_score = 10;
//            $item->score = 0;
//            $item->uri = route('report.show',['report' => $item->id]);
//            return $item;
//        });
        return null;
    }

}