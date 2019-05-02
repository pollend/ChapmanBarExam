<?php


namespace App\Http\Middleware;


use App\QuizSession;
use App\Repositories\SessionRepositoryInterface;
use Closure;
use Illuminate\Support\Facades\Auth;


class RedirectToQuizForSession
{

    private $sessionRepository;
    public function __construct(SessionRepositoryInterface $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
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
        $user = Auth::user();
        $quizSession = $this->sessionRepository->getActiveSession($user);
        if ($quizSession != null) {
            return redirect()->route('quiz.question', ['session_id' => $quizSession->id, 'page' => 0]);
        }
        return $next($request);
    }

}