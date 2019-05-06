<?php

/* @var $factory \LaravelDoctrine\ORM\Testing\Factory */
use App\Quiz;
use Faker\Generator as Faker;

$factory->define(\App\Entities\QuizSession::class, function (Faker $faker) {
    return [
        'submittedAt' => $faker->dateTimeBetween('-1 week','+1 month'),
    ];
});
