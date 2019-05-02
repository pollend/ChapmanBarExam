<?php

namespace App\Http\Controllers;

use App\Quiz;
use App\QuizSession;
use App\Repositories\QuizRepositoryInterface;
use App\Repositories\SessionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    private $quizRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(QuizRepositoryInterface $quizRepository)
    {
        $this->quizRepository = $quizRepository;
    }

    /**
     * Show the application home
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

        $quizzes = Quiz::query()->where('is_hidden', false)->get();
        foreach ($quizzes as $q){
            $q->is_open  = $this->quizRepository->isOpen($q,$user);
        }
        return view('quiz.index', ['quizzes' => $quizzes,'user' => $user]);
    }
}
