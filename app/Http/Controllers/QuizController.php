<?php


namespace App\Http\Controllers;


use App\Quiz;

class QuizController extends Controller
{
    public function showQuizStart(){
    }

    public function quizStart(){

    }

    public function quizSection($group){

    }

    public function index()
    {
        $quizzes = Quiz::all();

        return view('quiz.index',['quizzes' => $quizzes,'test' => 'test']);
    }

}