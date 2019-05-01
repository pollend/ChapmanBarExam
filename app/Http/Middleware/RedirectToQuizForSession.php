<?php


namespace App\Http\Middleware;


use App\QuizSession;
use App\Repositories\SessionRepositoryInterface;
use Closure;
use Illuminate\Support\Facades\Auth;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class RedirectToQuizForSession extends Middleware
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return mixed
     */
    protected function redirectTo($request)
    {
        $user = Auth::user();
        $quizSession = QuizSession::query()->where('owner_id', $user->id)->first();
        if ($quizSession != null) {
            return redirect()->route('quiz.question', ['session_id' => $quizSession->id, 'page' => 0]);
        }

    }

}