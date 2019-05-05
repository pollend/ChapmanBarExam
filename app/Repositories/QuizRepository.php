<?php


namespace App\Repositories;


use App\Quiz;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Collection;

class QuizRepository implements QuizRepositoryInterface
{
    /**
     * @param Quiz $quiz
     * @param $user
     * @return bool
     */
    public function  isOpen($quiz, $user)
    {
        if ($this->attempt_count($quiz,$user) >= $quiz->num_attempts)
            return false;

        if ($quiz->close_date < Carbon::today())
            return false;

        if ($quiz->submitted_at != null)
            return false;

        return true;
    }

    /**
     * @param Quiz $quiz
     * @param $user
     * @return mixed
     */
    public function attempt_count($quiz, $user)
    {
        return $quiz->sessions()->where('owner_id', $user->id)->count();
    }

    /**
     * @param \App\Quiz $quiz
     * @return \Illuminate\Support\Collection
     */
    public function getGroupedQuestions($quiz)
    {
        $collection = $this->getQuestions($quiz)
            ->sortBy('group')
            ->groupBy('group')
            ->transform(function ($entry) {
                return $entry->sortby('order')->values();
            })->values();
        return $collection;
    }

    /**
     * @param Quiz $quiz
     * @return \Illuminate\Support\Collection
     */
    public function getQuestions($quiz,\Closure $callback = null){
        if($callback){
            return \Illuminate\Support\Collection::make()
                ->merge($callback($quiz->multipleChoiceQuestions()))
                ->merge($callback($quiz->shortAnswerQuestions()));
        }
        return \Illuminate\Support\Collection::make()
            ->merge($quiz->multipleChoiceQuestions()->get())
            ->merge($quiz->shortAnswerQuestions()->get());
    }

    /**
     * get the order values for looking up questions
     *
     * @param Quiz $quiz
     * @return \Illuminate\Support\Collection
     */
    public function getGroups(Quiz $quiz)
    {
        return $this->getQuestions($quiz, function ($q) {
            return $q->distinct('group')->value('group');
        })->unique()
            ->sort()
            ->values();
    }


    function getUnionedQuestions(\Closure $query = null)
    {
        $q1 = DB::table('quiz_multiple_choice_questions')
            ->select('id', 'created_at', 'updated_at', 'order', 'group', 'quiz_id')
            ->selectRaw('\'multiple_choice\' as "type"');

        $q2 = DB::table('quiz_short_answer_questions')
            ->select('id', 'created_at', 'updated_at', 'order', 'group', 'quiz_id')
            ->selectRaw('\'short_answer\' as "type"');

        if($query != null)
            return $query($q1)->union($query($q2));
        return $q1->union($q2);
    }
}