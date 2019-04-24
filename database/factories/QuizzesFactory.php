<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Quiz;
use Faker\Generator as Faker;

$factory->define(Quiz::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->words($nb = 1, $asText = true),
        'close_date' => $faker->dateTimeBetween('-1 week','+1 month'),
        'num_attempts' => $faker->numberBetween($min = 0, $max = 5),
        'is_open' => $faker->boolean
    ];
});
