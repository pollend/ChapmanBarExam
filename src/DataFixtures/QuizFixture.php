<?php

namespace App\DataFixtures;

use App\Entity\MultipleChoiceEntry;
use App\Entity\MultipleChoiceQuestion;
use App\Entity\QuestionTag;
use App\Entity\Quiz;
use App\Entity\TextBlockQuestion;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\Collection;

class QuizFixture extends Fixture implements DependentFixtureInterface
{
    private $tags;

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return array
     */
    public function getDependencies()
    {
        return [TagFixture::class];
    }

    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->tags = $manager->getRepository(QuestionTag::class)->findAll();

        $faker = Factory::create();

        $quizzes = Collection::times(20, function ($index) use ($faker,$manager) {
            $quiz = new Quiz();
            $quiz->setDescription($faker->words($nb = 1, $asText = true))
                ->setName($faker->name);
            $manager->persist($quiz);

            return $quiz;
        });
        $manager->flush();

        $quizzes->each(function ($quiz) use ($faker,$manager) {
            $this->generateMultipleChoiceQuestionSet($manager, 100, $faker)
                ->each(function ($q) use ($quiz,$manager) {
                    /* @var MultipleChoiceQuestion $q */
                    $q->setGroup(0);
                    $q->setQuiz($quiz);
                    $manager->persist($q);
                });
            $manager->persist((new TextBlockQuestion())
                ->setContent($faker->paragraph($nbSentences = 15, $variableNbSentences = true))
                ->setOrder(0)
                ->setGroup(1)
                ->setQuiz($quiz));

            $this->generateMultipleChoiceQuestionSet($manager, 100, $faker)
                ->each(function ($q) use ($quiz,$manager) {
                    /* @var MultipleChoiceQuestion $q */
                    $q->setGroup(2);
                    $q->setQuiz($quiz);
                    $manager->persist($q);
                });
            $manager->flush();
        });
    }

    /**
     * @param $amount
     * @param Generator $faker
     *
     * @return Collection
     */
    private function generateMultipleChoiceQuestionSet(ObjectManager $manager, $amount, $faker)
    {
        return Collection::times($amount, function ($index) use ($faker,$manager) {
            $multipleChoiceQuestion = new MultipleChoiceQuestion();
            $multipleChoiceQuestion
                ->setOrder($index)
                ->setContent($faker->paragraph($nbSentences = 15, $variableNbSentences = true));

            $entires = Collection::times(rand(3, 4), function ($index) use ($multipleChoiceQuestion,$faker,$manager) {
                $entry = new MultipleChoiceEntry();
                $entry->setContent($faker->sentence($nbWords = 10, $variableNbWords = true))
                    ->setQuestion($multipleChoiceQuestion)
                    ->setOrder($index);
                $manager->persist($entry);

                return $entry;
            });

            foreach (Collection::make($this->tags)->random(10) as $tag) {
                $multipleChoiceQuestion->getTags()->add($tag);
            }

            $multipleChoiceQuestion->setCorrectAnswer($entires->random(1)[0]);

            return $multipleChoiceQuestion;
        });
    }
}
