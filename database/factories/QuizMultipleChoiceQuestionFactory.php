<?php


/* @var $factory \LaravelDoctrine\ORM\Testing\Factory */

use Faker\Generator as Faker;

$factory->define(\App\Entities\MultipleChoiceQuestion::class, function (Faker $faker) {
    return [
        'content' => $faker->paragraph($nbSentences = 15, $variableNbSentences = true),
    ];
});

