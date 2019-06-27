<?php
namespace App\DataFixtures;


use App\Entity\Classroom;
use App\Entity\MultipleChoiceQuestion;
use App\Entity\MultipleChoiceResponse;
use App\Entity\Quiz;
use App\Entity\QuizAccess;
use App\Entity\QuizSession;
use App\Entity\User;
use App\Repository\ClassroomRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Illuminate\Support\Collection;
use PhpParser\Node\Expr\BinaryOp\Equal;
use Symfony\Component\Console\Output\ConsoleOutput;

class ResponseFixture extends Fixture implements DependentFixtureInterface
{

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [QuizFixture::class,ClassroomFixture::class];
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $output = new ConsoleOutput();
        /** @var ClassroomRepository $classroomRepository */
        $classroomRepository = $manager->getRepository(Classroom::class);

        $classrooms = $classroomRepository->findAll() ;
        /** @var Classroom $classroom */
        foreach ($classrooms as $key => $classroom) {
            $output->writeln('classroom:' . $key . '/' . count($classrooms));

            /** @var User $user */
            foreach (Collection::make($classroom->getUsers())->random(20) as $user) {
                foreach ($classroom->getQuizAccess() as $access) {
                    if (rand(0, 10) < 5) {
                        for ($i = 0; $i < rand(0, 5); $i++) {
                            /** @var Quiz $quiz */
                            $quiz = $access->getQuiz();
                            $session = new QuizSession();
                            $session->setQuiz($quiz);
                            $session->setClassroom($classroom);
                            $session->setQuizAccess($access);
                            $session->setOwner($user);
                            $session->setMeta([]);
                            $session->setSubmittedAt($faker->dateTime());
                            $manager->persist($session);

                            foreach ($quiz->getQuestions() as $question) {
                                if (rand(0, 10) < 6) {
                                    if ($question instanceof MultipleChoiceQuestion) {
                                        $response = new MultipleChoiceResponse();
                                        $response->setSession($session);
                                        $response->setChoice(Collection::make($question->getEntries())->random(1)[0]);
                                        $response->setQuestion($question);
                                        $manager->persist($response);
                                    }
                                }
                            }

                        }
                    }
                }
            }

            $manager->flush();
        }
        $manager->flush();
    }

}