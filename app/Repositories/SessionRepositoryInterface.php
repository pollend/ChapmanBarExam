<?php


namespace App\Repositories;


interface SessionRepositoryInterface
{

    function startSession($user, $quiz);


    function getActiveSession($user);


}