<?php


namespace App\Http\Middleware;


use App\Entities\QuizSession;
use App\Repositories\QuizSessionRepository;
use App\Repositories\SessionRepositoryInterface;
use Closure;
use Illuminate\Support\Facades\Auth;


class RedirectToQuizForSession
{
    public function __construct()
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $repository = \EntityManager::getRepository(QuizSession::class);
        $user = Auth::user();
        $quizSession = $repository->getActiveSession($user);
        if ($quizSession != null) {
            return redirect()->route('quiz.question', ['session_id' => $quizSession->id, 'page' => 0]);
        }
        return $next($request);
    }

}