<?php


namespace App\Repositories;


use App\Quiz;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class QuizRepository implements QuizRepositoryInterface
{
    public function isOpen($quiz, $user)
    {
        if ($quiz->sessions()->where('owner_id', $user->id)->count() >= $quiz->num_attempts)
            return false;

        if ($quiz->close_date < Carbon::today())
            return false;

        if ($quiz->is_open == false)
            return false;

        return true;
    }

    public function attempt_count($quiz, $user)
    {
        return $quiz->sessions()->where('owner_id', $user->id)->count();
    }

    /**
     * @param \App\Quiz $quiz
     * @return \Illuminate\Support\Collection
     */
    public function getQuestions($quiz)
    {
        $collection = \Illuminate\Support\Collection::make()->merge($quiz->multipleChoiceQuestions()->get())
            ->merge($quiz->shortAnswerQuestions()->get())
            ->sortBy('group')
            ->groupBy('group');

        $index = 0;
        $final = [];
        foreach ($collection as $key => $value) $final[$index++] = $value;
        return \Illuminate\Support\Collection::make($final);
    }

    /**
     * get the order values for looking up questions
     *
     * @param Quiz $quiz
     * @return \Illuminate\Support\Collection
     */
    public function getGroups(Quiz $quiz)
    {
       return \Illuminate\Support\Collection::make()
           ->merge($quiz->multipleChoiceQuestions()->distinct('group')->value('group'))
           ->merge($quiz->shortAnswerQuestions()->distinct('group')->value('group'))
           ->unique()
           ->sort()
           ->values();
    }
}