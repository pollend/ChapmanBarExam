<?php

/* @var $factory \LaravelDoctrine\ORM\Testing\Factory */
use Faker\Generator as Faker;

$factory->define(App\Entities\Quiz::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->words($nb = 1, $asText = true)
    ];
});
