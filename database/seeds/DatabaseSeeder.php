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
            factory(\App\QuizMultipleChoiceQuestion::class,40)->create([
                'quiz_id' => $quiz->id,
                'group' => 1,
                'order' => function(array $i) use (&$index){
                    return ++$index;
                }
            ])->each(function ($q){
              $q_index = 0;

              factory(\App\QuizMultipleChoiceEntry::class,rand(3,4))->create([
                  'order' => function(array $i) use(&$q_index){
                      return ++$q_index;
                   },
                  'quiz_multiple_choice_question_id' => $q->id
              ]);
            });

            factory(\App\QuizShortAnswerQuestion::class)->create([
                'quiz_id' => $quiz->id,
                'group' => 1,
                'order' => 0
            ]);

            factory(\App\QuizShortAnswerQuestion::class,3)->create([
                'quiz_id' => $quiz->id,
                'group' => 0,
                'order' => function(array $i) use (&$index){
                    return ++$index;
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