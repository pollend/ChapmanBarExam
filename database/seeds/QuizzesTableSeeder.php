<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
class QuizzesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $quizzes = factory(\App\Quiz::class,10)->create();
    }
}
