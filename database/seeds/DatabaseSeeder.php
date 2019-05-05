<?php

use App\Quiz;
use App\QuizMultipleChoiceEntry;
use App\QuizMultipleChoiceQuestion;
use App\QuizShortAnswerQuestion;
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

        $quizzes = factory(Quiz::class, 20)->create();
        $users = factory(User::class, 10)->create();
        foreach ($quizzes as $key => $quiz) {
            print('quiz: '. $key . "\r\n");
            $index = 0;

            $sessions = factory(\App\QuizSession::class,10)->create([
                'owner_id' => Arr::random($users->toArray())['id'],
                'quiz_id' => $quiz->id
            ]);

            factory(QuizMultipleChoiceQuestion::class, 40)->create([
                'quiz_id' => $quiz->id,
                'group' => 1,
                'order' => function (array $i) use (&$index) {
                    return ++$index;
                }
            ])->each(function ($q) use ($sessions) {
                $q_index = 0;

                $entries = factory(QuizMultipleChoiceEntry::class, rand(3, 4))->create([
                    'order' => function (array $i) use (&$q_index) {
                        return ++$q_index;
                    },
                    'quiz_multiple_choice_question_id' => $q->id
                ]);
                $q->quiz_multiple_choice_entry_id = Arr::random($entries->toArray())['id'];
                $q->save();

                foreach ($sessions as $session){
                    factory(\App\QuizMultipleChoiceResponse::class)->create([
                        'quiz_multiple_choice_entry_id' => Arr::random($entries->toArray())['id'],
                        'quiz_session_id' => $session->id,
                        'quiz_multiple_choice_question_id' => $q->id
                    ]);
                }

            });

            factory(QuizShortAnswerQuestion::class)->create([
                'quiz_id' => $quiz->id,
                'group' => 1,
                'order' => 0
            ]);

            factory(QuizShortAnswerQuestion::class, 3)->create([
                'quiz_id' => $quiz->id,
                'group' => 0,
                'order' => function (array $i) use (&$index) {
                    return ++$index;
                }
            ]);


        }


    }
}