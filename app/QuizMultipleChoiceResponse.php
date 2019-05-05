<?php


namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class QuizMultipleChoiceResponse extends Model implements QuizResponse
{
    public $timestamps = false;

    public function session()
    {
        return $this->belongsTo('App\QuizSession', 'quiz_session_id', 'id');
    }

    public function entry()
    {
        return $this->belongsTo('App\QuizMultipleChoiceEntry', 'quiz_multiple_choice_entry_id', 'id');
    }

    public function question()
    {
        return $this->belongsTo('App\QuizMultipleChoiceQuestion', 'quiz_multiple_choice_question_id', 'id');
    }

    /**
     * Scope query to only include responses by session
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\QuizSession $session
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBySession($query, $session)
    {
        return $query->where('quiz_session_id', $session->id);
    }
    /**
     * Scope query to only include responses by session
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\QuizSession $session
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCorrectEntry($query){

        return $query->select('quiz_multiple_choice_responses.*')
            ->join('quiz_multiple_choice_questions','quiz_multiple_choice_questions.id','=','quiz_multiple_choice_responses.quiz_multiple_choice_question_id')
            ->selectSub( function ($query){
             return $query
                 ->select('quiz_multiple_choice_questions.quiz_multiple_choice_entry_id')
                 ->whereColumn('quiz_multiple_choice_responses.quiz_multiple_choice_entry_id','quiz_multiple_choice_questions.quiz_multiple_choice_entry_id');
        },'quiz_multiple_choice_correct_entry_id');
    }
}