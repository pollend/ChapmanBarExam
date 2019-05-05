<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Quiz;
use Faker\Generator as Faker;

$factory->define(\App\QuizSession::class, function (Faker $faker) {
    return [
        'submitted_at' => $faker->dateTimeBetween('-1 week','+1 month'),
    ];
});
