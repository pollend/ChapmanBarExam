<?php


namespace App\Http\Middleware;


use App\Repositories\QuizSessionRepository;
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

        $user = \Auth::user();
        /** @var QuizSessionRepository $repository */
        $repository = \EntityManager::getRepository(\App\Entities\QuizSession::class);

        if ($session =  $repository->getActiveSession($user)) {
            return redirect()->route('quiz.question', ['session_id' => $session->getId(), 'page' => 0]);
        }
        return $next($request);
    }

}