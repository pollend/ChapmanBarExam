<?php


namespace App\Http\Controllers;


use App\Quiz;
use App\QuizSession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SessionQuizController extends Model
{

    public function startForm($id){
        return view('quiz.start',['quiz_id' => $id]);
    }

    public function start($id){
        $quiz = Quiz::where('id', $id)->firstOrFail();
        $user = Auth::user();

        $session = new QuizSession();
        $session->owner_id = $user->id;
        $session->quiz_id = $quiz->id;
        $session->save();

        return redirect()->route('quiz.question',['session_id'=>$session->id,'page' => 0]);
    }


    public function questionForm($session_id,$page){

    }

    public function question($session_id){

    }

}