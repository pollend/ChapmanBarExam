<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class QuizShortAnswerResponse extends Model implements QuizResponse
{
    public $timestamps = false;
    public function session()
    {
        return $this->belongsTo('App\QuizSession', 'quiz_session_id', 'id');
    }


    public function question()
    {
        return $this->belongsTo('App\QuizShortAnswerQuestion', 'quiz_short_answer_question_id', 'id');
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
}