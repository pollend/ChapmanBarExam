<?php


/* @var $factory \LaravelDoctrine\ORM\Testing\Factory */

use Faker\Generator as Faker;

$factory->define(\App\Entities\ShortAnswerQuestion::class, function (Faker $faker) {
    return [
        'content' => $faker->paragraph($nbSentences = 15, $variableNbSentences = true)
    ];
});
