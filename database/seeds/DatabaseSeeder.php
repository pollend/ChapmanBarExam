<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $quizzes = factory(\App\Quiz::class,10)->create();
        $users = factory(\App\User::class,10)->create();

    }
}