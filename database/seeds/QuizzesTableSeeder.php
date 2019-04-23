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
        DB::table('quizzes')->insert([
           'uuid' =>  \Ramsey\Uuid\Uuid::generate()->string,

        ]);
    }
}
