<?php

/* @var $factory \LaravelDoctrine\ORM\Testing\Factory */
use App\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(\App\Entities\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'emailVerifiedAt' => now(),
        'password' => bcrypt('password'), // password
        'azureId' => '--',
        'rememberToken' => Str::random(10),
    ];
});
