<?php


namespace App\Repositories;


use App\Exceptions\SessionInProgress;
use App\QuizSession;

class SessionController implements SessionControllerInterface
{
    public function startSession($user_id,$quiz_id){
        if(QuizSession::query()->where('owner_id',$user_id)->count() > 0){
            throw new SessionInProgress();
        }
        return QuizSession::query()->create([
            'quiz_id' => $quiz_id,
            'owner_id' => $user_id
        ]);

    }
}