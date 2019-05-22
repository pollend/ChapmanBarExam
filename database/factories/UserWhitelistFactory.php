<?php

/* @var $factory \LaravelDoctrine\ORM\Testing\Factory */

use App\Entities\Quiz;
use Faker\Generator as Faker;

$factory->define(App\Entities\UserWhitelist::class, function (Faker $faker) {
    return [
        'email' => $faker->unique()->safeEmail
    ];
});
