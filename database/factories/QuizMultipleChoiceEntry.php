<?php


/* @var $factory \LaravelDoctrine\ORM\Testing\Factory */


use Faker\Generator as Faker;

$factory->define(\App\Entities\MultipleChoiceEntry::class, function (Faker $faker) {
    return [
        'content' => $faker->sentence($nbWords = 10 , $variableNbWords = true),
    ];
});

