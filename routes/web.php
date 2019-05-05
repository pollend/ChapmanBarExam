<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authentication Routes...
use Illuminate\Http\Request;

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
//Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
//Route::post('register', 'Auth\RegisterController@register');

// Email Verification Routes...
//Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
//Route::get('email/verify/{id}', 'Auth\VerificationController@verify')->name('verification.verify');
//Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');

// Password Reset Routes...
//Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
//Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
//Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
//Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');


Route::get('login/azure', 'Auth\SocialAuthController@redirectAzureToProvider')->name('login.azure');
Route::get('login/azure/callback', 'Auth\SocialAuthController@handleAzureProviderCallback');

Route::group(['middleware' => ['auth']], function (){

    // redirect if there is an active session
    Route::group(['middleware' => ['quiz.redirect']],function (){
        Route::resource('report','ReportController');

        Route::get('/', 'HomeController@index')->name('home');

        Route::get('quiz/end','SessionQuizController@endForm')->name('quiz.end');
        Route::post('quiz/end','SessionQuizController@end');

        Route::get('quiz/start/{id}','SessionQuizController@startForm')->name('quiz.start');
        Route::post('quiz/start/{id}','SessionQuizController@start');
    });

    Route::get('session/{session_id}/page/{page}','SessionQuizController@questionForm')->name('quiz.question');
    Route::post('session/{session_id}/page/{page}','SessionQuizController@question');
    Route::namespace('dashboard')->prefix('admin')->group(function () {

    });
});
