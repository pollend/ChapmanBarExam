<?php

namespace App\Command;

use App\Entity\MultipleChoiceEntry;
use App\Entity\MultipleChoiceQuestion;
use App\Entity\QuestionTag;
use App\Entity\Quiz;
use App\Entity\TextBlockQuestion;
use App\Repository\QuestionTagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class ImportExamCsvCommand extends Command
{
    protected static $defaultName = 'app:import-exam-csv';

    private $entityManager;
    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        parent::__construct(null);
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }


    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('file', InputArgument::REQUIRED, 'an exam csv to load into the database')
            ->addArgument('title', InputArgument::REQUIRED, 'title of an exam')
            ->addArgument('description', InputArgument::OPTIONAL, 'the description of the exam');;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');
        $description = $input->getArgument('description');
        if (!file_exists($file)) {
            $io->error(sprintf('unknown file:', $file));
        }
        $io->note(sprintf('Loading_csv: %s', $file));

        $payload = $this->serializer->decode(file_get_contents($file), 'csv');

        $tags = [];

        $quiz = new Quiz();

        $quiz->setDescription($description ? $description : '');
        $quiz->setName($input->getArgument('title'));
        $this->entityManager->persist($quiz);
        $count = 0;
        $group = 0;
        foreach ($payload as $row) {
            $count++;
            if ($count === 100) {
                $group++;

                $textBlockQuestion = new TextBlockQuestion();
                $textBlockQuestion->setContent(" This is the end of the first part");
                $textBlockQuestion->setQuiz($quiz);
                $textBlockQuestion->setGroup($group);
                $textBlockQuestion->setOrder(0);
                $this->entityManager->persist($textBlockQuestion);
                $group++;
            }

            $multipleChoiceQuestion = new MultipleChoiceQuestion();
            $entries = [];
            foreach (['a' => $row['a'], 'b' => $row['b'], 'c' => $row['c'], 'd' => $row['d']] as $key => $value) {
                $q = new MultipleChoiceEntry();
                $q->setContent($value)
                    ->setOrder($count)
                    ->setQuestion($multipleChoiceQuestion);
                $entries[$key] = $q;
                $this->entityManager->persist($q);
            }
            /** @var QuestionTagRepository $questionTagRepository */
            $questionTagRepository = $this->entityManager->getRepository(QuestionTag::class);

            $multipleChoiceQuestion->setCorrectAnswer($entries[$row['answer']]);
            $multipleChoiceQuestion->setGroup($group);
            $multipleChoiceQuestion->setOrder($count);
            $multipleChoiceQuestion->setQuiz($quiz);
            $multipleChoiceQuestion->setContent($row['question']);
            foreach (explode(',',$row['tags']) as $name){
                $name = trim($name);
                if(empty($name))
                    continue;

                /** @var QuestionTag $tag */
                $tag = array_key_exists($name,$tags) ? $tags[$name] : null;
                if(!$tag) {
                    $tag = $questionTagRepository->byName($name);
                    if (!$tag) {
                        $tag = new QuestionTag();
                        $tag->setName($name);
                    }
                    $this->entityManager->persist($tag);
                }
                $tags[$name] = $tag;
                $multipleChoiceQuestion->getTags()->add($tag);
            }
            $this->entityManager->persist($multipleChoiceQuestion);
        }
        $this->entityManager->flush();
    }
}