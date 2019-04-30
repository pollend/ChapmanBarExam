<?php

namespace App\Http\Controllers;

use App\Quiz;
use App\QuizSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application home
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

        $quizzes = Quiz::all();
        foreach ($quizzes as $quiz) {
            // get's the number of attempts based off the user
            $quiz->attempts = $quiz->quizSessions()
                ->where('owner_id', $user->id)->count();
            // determines if the quiz is locked
            $quiz->locked = ($quiz->attempts > $quiz->num_attempts || !$quiz->is_open);
        }

        return view('quiz.index', ['quizzes' => $quizzes, 'test' => 'test']);
    }
}
