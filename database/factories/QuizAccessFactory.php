<?php

/* @var $factory \LaravelDoctrine\ORM\Testing\Factory */
use Faker\Generator as Faker;

$factory->define(App\Entities\QuizAccess::class, function (Faker $faker) {
    $startDate = $faker->dateTimeBetween('-1 week', '+1 month');
    $endDate = $faker->dateTimeBetween('-1 week', '+1 week');

    if($startDate > $endDate){
        $temp = $startDate;
        $endDate = $startDate;
        $startDate = $temp;
    }

    return [
        'openDate' => $startDate,
        'closeDate' => $endDate,
        'numAttempts' => $faker->numberBetween($min = 0, $max = 5),
        'isHidden' => $faker->boolean
    ];
});
