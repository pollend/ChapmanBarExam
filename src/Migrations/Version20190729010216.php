<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Quiz;
use App\Repository\QuizRepository;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190729010216 extends AbstractMigration implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * Sets the container.
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getDescription() : string
    {
        return 'Add Exam Answers';
    }

    public function up(Schema $schema) : void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->container->get('doctrine.orm.entity_manager');

        $application = new Application($this->container->get('kernel'));
        $application->setAutoExit(false);
        /** @var QuizRepository $quizRepository */
        $quizRepository = $em->getRepository(Quiz::class);
        if(!$quizRepository->byName('Mock Bar 1')) {
            $application->run(new ArrayInput([
                'command' => 'app:import-exam-csv',
                'file' => 'exams/mb1.csv',
                'title' => 'Mock Bar 1'
            ]));
        }

        if(!$quizRepository->byName('Mock Bar 2')) {
            $application->run(new ArrayInput([
                'command' => 'app:import-exam-csv',
                'file' => 'exams/mb2.csv',
                'title' => 'Mock Bar 2'
            ]));
        }
        if(!$quizRepository->byName('Mock Bar 3')) {
            $application->run(new ArrayInput([
                'command' => 'app:import-exam-csv',
                'file' => 'exams/mb3.csv',
                'title' => 'Mock Bar 3'
            ]));
        }

    }

    public function down(Schema $schema) : void
    {

    }

}
