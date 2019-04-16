<?php


namespace App\Http\Controllers;


class QuizController extends Controller
{
    public function index()
    {
        return view('quiz.index');
    }

}