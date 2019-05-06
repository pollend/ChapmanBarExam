<?php

/* @var $factory \LaravelDoctrine\ORM\Testing\Factory */
use Faker\Generator as Faker;

$factory->define(App\Entities\Quiz::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->words($nb = 1, $asText = true),
        'closeDate' => $faker->dateTimeBetween('-1 week','+1 month'),
        'numAttempts' => $faker->numberBetween($min = 0, $max = 5),
        'isOpen' => $faker->boolean,
        'isHidden' => $faker->boolean
    ];
});
