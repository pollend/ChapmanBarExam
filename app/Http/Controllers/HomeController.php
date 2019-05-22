<?php

namespace App\Http\Controllers;

use App\Entities\Classroom;
use App\Entities\Quiz;
use App\Repositories\ClassroomRepository;
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

        /** @var ClassroomRepository $classroomRepository */
        $classroomRepository = \EntityManager::getRepository(Classroom::class);
        $user = Auth::user();
        $classrooms = $classroomRepository->getClassroomsForUser($user);
        return view('quiz.index', ['classrooms' => Collection::make($classrooms),'user' => $user]);
    }
}
