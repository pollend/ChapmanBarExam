<?php


/* @var $factory \Illuminate\Database\Eloquent\Factory */


use Faker\Generator as Faker;

$factory->define(\App\QuizMultipleChoiceEntry::class, function (Faker $faker) {
    return [
        'content' => $faker->sentence($nbWords = 10 , $variableNbWords = true),
    ];
});

