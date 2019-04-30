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


Route::get('login/azure', 'Auth\LoginController@redirectToProvider');
Route::get('login/azure/callback', 'Auth\LoginController@handleProviderCallback');


Route::get('/', 'HomeController');
Route::resource('report','ReportController');


Route::namespace('Dashboard')->prefix('admin')->group(function () {

});



