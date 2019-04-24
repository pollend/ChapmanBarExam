<?php


namespace App\Http\Controllers;


use App\Quiz;

class HomeController extends Controller
{
    public function __invoke()
    {

        $quizzes = Quiz::all();

        return view('quiz.index',['quizzes' => $quizzes]);
    }
}