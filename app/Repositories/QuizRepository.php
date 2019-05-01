<?php


namespace App\Repositories;


use Carbon\Carbon;

class QuizRepository implements QuizRepositoryInterface
{
    public function isOpen($quiz, $user)
    {
        if ($quiz->quizSessions()->where('owner_id', $user->id)->count() >= $quiz->num_attempts)
            return false;
        if($quiz->close_date < Carbon::today())
            return false;
        if ($quiz->is_open == false)
            return false;
        return true;
    }

    public function attempt_count($quiz, $user)
    {
        return $quiz->quizSessions()->where('owner_id', $user->id)->count();
    }

    public function getQuestions($quiz){
        $collection = new \Illuminate\Support\Collection();
        foreach ($quiz->multipleChoiceQuestions()->get() as $q){
            $collection->add($q);
        }
        foreach ($quiz->shortAnswerQuestions()->get() as $q){
            $collection->add($q);
        }
        $collection = $collection->sortBy('group');
        $collection = $collection->groupBy('group');

        $index = 0;
        $final = [];
        foreach ($collection as $key => $value) $final[$index++] = $value;
        return \Illuminate\Support\Collection::make($final);
    }

    public function getAnswer($user,$question){
        switch ($question->type){
            case 'multipleChoice':
                
                break;
            case 'shortAnswer':
                break;
        }
    }


}