<?php

namespace App\Providers;

use App\Repositories\QuizRepository;
use App\Repositories\QuizRepositoryInterface;
use App\Repositories\SessionRepository;
use App\Repositories\SessionRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SessionRepositoryInterface::class,SessionRepository::class);
        $this->app->bind(QuizRepositoryInterface::class,QuizRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
