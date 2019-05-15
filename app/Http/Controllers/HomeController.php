<?php

namespace App\Http\Controllers;

use App\Repositories\QuizRepository;
use Doctrine\Common\Collections\Criteria;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
    }

    /**
     * Show the application home
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        /** @var QuizRepository $repository */
        $repository = \EntityManager::getRepository(\App\Entities\Quiz::class);
        $quizzes  = $repository->findBy(['isHidden' => false]);
        return view('quiz.index', ['quizzes' => Collection::make($quizzes),'user' => $user]);
    }
}
