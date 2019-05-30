<?php

use App\Entities\MultipleChoiceEntry;
use App\Entities\MultipleChoiceQuestion;
use Illuminate\Database\Seeder;

use Faker\Factory as Faker;

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
        $users = entity(\App\Entities\User::class, 200)->create();
        $admin = entity(\App\Entities\User::class)->create([
                'isAdmin' => true
            ]);
        $tags = entity(\App\Entities\QuestionTag::class,50)->create();

        foreach ($quizzes as $key => $quiz) {

            print('quiz: '. $key . "\r\n");
            $index = 0;


            entity(App\Entities\MultipleChoiceQuestion::class, 100)->create([
                'quiz' => $quiz,
                'group' => 0,
                'order' => function (array $i) use (&$index) {
                    return ++$index;
                }
            ])->each(function ($q) use ($tags){
                $q_index = 0;

                $entries = entity(App\Entities\MultipleChoiceEntry::class, rand(3, 4))->create([
                    'order' => function (array $i) use (&$q_index) {
                        return ++$q_index;
                    },
                    'question' => $q
                ]);
                foreach ($tags->random(rand(1,4)) as $tag)
                    $q->getTags()->add($tag);

                $q->setCorrectAnswer($entries->random(1)[0]);
                \EntityManager::persist($q);

            });


            entity(App\Entities\TextBlockQuestion::class, 1)->create([
                'quiz' => $quiz,
                'group' => 1,
                'order' => function (array $i) use (&$index) {
                    return ++$index;
                }
            ]);

            entity(App\Entities\MultipleChoiceQuestion::class, 100)->create([
                'quiz' => $quiz,
                'group' => 2,
                'order' => function (array $i) use (&$index) {
                    return ++$index;
                }
            ])->each(function ($q)  use ($tags){
                $q_index = 0;

                $entries = entity(App\Entities\MultipleChoiceEntry::class, rand(3, 4))->create([
                    'order' => function (array $i) use (&$q_index) {
                        return ++$q_index;
                    },
                    'question' => $q
                ]);

                foreach ($tags->random(rand(1,4)) as $tag)
                    $q->getTags()->add($tag);

                $q->setCorrectAnswer($entries->random(1)[0]);
                \EntityManager::persist($q);

            });
            \EntityManager::flush();

        }

        $classrooms = entity(\App\Entities\Classroom::class,10)->create();
        /** @var \App\Entities\Classroom $classroom */
        foreach ($classrooms as $key => $classroom) {
            \Illuminate\Support\Collection::make($users)->random(100)->each(function ($u) use ($classroom) {
                /** @var \App\Entities\UserWhitelist $whitelist */
                $whitelist = entity(\App\Entities\UserWhitelist::class)->create();
                $whitelist->setEmail($u->getEmail());
                $whitelist->setClassroom($classroom);
                \EntityManager::persist($whitelist);
            });

            foreach(\Illuminate\Support\Collection::make($quizzes)->random(rand(4,10)) as $quiz){
                /** @var \App\Entities\QuizAccess $access */
                $access = entity(\App\Entities\QuizAccess::class)->create();
                $access->setQuiz($quiz);
                $access->setClassroom($classroom);
                \EntityManager::persist($access);
            }

        }


        \EntityManager::flush();
    }
}