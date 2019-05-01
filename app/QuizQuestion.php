<?php


namespace App;


interface QuizQuestion
{
    function getTypeAttribute();

    function answers();


}