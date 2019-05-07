<?php

use App\Quiz;
use App\MultipleChoiceEntry;
use App\MultipleChoiceQuestion;
use App\ShortAnswerQuestion;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $quizzes = entity(\App\Entities\Quiz::class, 20)->create();
        $users = entity(\App\Entities\User::class, 10)->create();

        foreach ($quizzes as $key => $quiz) {

            print('quiz: '. $key . "\r\n");
            $index = 0;

            $sessions = entity(\App\Entities\QuizSession::class,10)->create([
                'owner' => $users->random(1)[0],
                'quiz' => $quiz
            ]);

            entity(App\Entities\MultipleChoiceQuestion::class, 40)->create([
                'quiz' => $quiz,
                'group' => 1,
                'order' => function (array $i) use (&$index) {
                    return ++$index;
                }
            ])->each(function ($q) use ($sessions) {
                $q_index = 0;

                $entries = entity(App\Entities\MultipleChoiceEntry::class, rand(3, 4))->create([
                    'order' => function (array $i) use (&$q_index) {
                        return ++$q_index;
                    },
                    'quizMultipleChoice' => $q
                ]);
                $q->setCorrectAnswer($entries->random(1)[0]);
//                foreach ($sessions as $session){
//                    entity(App\Entities\MultipleChoiceResponse::class)->create([
//                        'multipleChoiceEntry' => $entries->random(1)[0],
//                        'session' => $session
//                    ]);
//                }

                \EntityManager::persist($q);

            });

            entity(App\Entities\ShortAnswerQuestion::class)->create([
                'quiz' => $quiz,
                'group' => 1,
                'order' => 0
            ]);

            entity(App\Entities\ShortAnswerQuestion::class, 3)->create([
                'quiz' => $quiz,
                'group' => 0,
                'order' => function (array $i) use (&$index) {
                    return ++$index;
                }
            ]);


        }

        \EntityManager::flush();
    }
}