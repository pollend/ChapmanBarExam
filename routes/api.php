<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:api'], 'as' => 'api.'],function (){
    Route::group(['middleware' => ['auth.admin'], 'prefix' => 'dashboard','as' => 'dashboard.'], function (){
        Route::resource('classes','Api\Dashboard\ClassroomController');
    });

   Route::resource('report','Api\ReportController');
   Route::get('report/{report}/meta','Api\ReportController@meta')->name('api.report.meta');

});
