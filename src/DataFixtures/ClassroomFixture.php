<?php


namespace App\DataFixtures;


use App\Entity\Classroom;
use App\Entity\Quiz;
use App\Entity\QuizAccess;
use App\Entity\User;
use App\Entity\UserWhitelist;
use App\Repository\ClassroomRepository;
use App\Repository\QuizRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Illuminate\Support\Collection;

class ClassroomFixture extends Fixture implements DependentFixtureInterface
{

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [UserFixture::class, QuizFixture::class];
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();


        /** @var UserRepository $userRepository */
        $userRepository = $manager->getRepository(User::class);
        /** @var QuizRepository $classroomRepository */
        $quizRepository = $manager->getRepository(Quiz::class);


        $users = $userRepository->findAll();
        $quizzes = $quizRepository->findAll();

        Collection::times(100,function ($index) use ($faker,$quizzes,$users,$manager) {
            $classroom = new Classroom();
            $classroom->setName($faker->name);
            $classroom->setDescription($faker->sentence($nbWords = 6, $variableNbWords = true));
            $classroom->setCourseNumber($faker->lexify('\#?-??'));
            $manager->persist($classroom);

            /** @var User $user */
            foreach (Collection::make($users)->random(100) as $user) {
                $whitelist = new UserWhitelist();
                $whitelist->setEmail($user->getEmail())
                    ->setClassroom($classroom);
                $manager->persist($whitelist);
            }

            /** @var Quiz $quiz */
            foreach (Collection::make($quizzes)->random(rand(4,10)) as $quiz){
                $access = new QuizAccess();
                $startDate = $faker->dateTimeBetween('-1 week', '+1 month');
                $endDate =  $faker->dateTimeBetween('-1 week', '+1 week');
                if($startDate > $endDate){
                    $temp = $startDate;
                    $endDate = $startDate;
                    $startDate = $temp;
                }

                $access->setQuiz($quiz)
                    ->setClassroom($classroom)
                    ->setCloseDate($endDate)
                    ->setOpenDate($startDate)
                    ->setNumAttempts($faker->numberBetween($min = 0,$max = 10))
                    ->setIsHidden($faker->boolean);
                $manager->persist($access);
            }
            $manager->flush();
        });
    }
}