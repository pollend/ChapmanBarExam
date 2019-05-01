<?php


namespace App\Http\Middleware;


use App\QuizSession;
use Closure;
use Illuminate\Support\Facades\Auth;


class RedirectToQuizForSession
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $quizSession = QuizSession::query()->where('owner_id', $user->id)->first();
        if ($quizSession != null) {
            return redirect()->route('quiz.question', ['session_id' => $quizSession->id, 'page' => 0]);
        }
        $next($request);
    }

}