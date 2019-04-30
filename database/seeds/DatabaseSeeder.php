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
        foreach($quizzes as $quiz){
            $index = 0;
            $multiple_choice_questions = factory(\App\QuizMultipleChoiceQuestion::class,40)->create([
                'quiz_id' => $quiz->id,
                'group' => 1,
                'order' => function(array $i) use (&$index){
                    $index+=1;
                    return $index;
                }
            ]);

            factory(\App\QuizShortAnswerQuestion::class,3)->create([
                'quiz_id' => $quiz->id,
                'group' => 0,
                'order' => function(array $i) use (&$index){
                    $index+=1;
                    return $index;
                }
            ]);
//                ->each(function ($i) use ($index) {
//                $index++;
//                $i->order = $index;
//            });



        }
        $users = factory(\App\User::class,10)->create();

    }
}