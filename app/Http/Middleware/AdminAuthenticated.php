<?php


namespace App\Http\Middleware;


use App\Entities\User;
use App\Repositories\QuizSessionRepository;
use Closure;

class AdminAuthenticated
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
        if(!$user->isAdmin()){
            abort(404);
        }
        return $next($request);
    }
}