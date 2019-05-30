<?php


/* @var $factory \LaravelDoctrine\ORM\Testing\Factory */

use Faker\Generator as Faker;

$factory->define(\App\Entities\QuestionTag::class, function (Faker $faker) {
    return [
        'name' => $faker->name
    ];
});

