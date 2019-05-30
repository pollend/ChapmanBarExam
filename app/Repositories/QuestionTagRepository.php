<?php


namespace App\Repositories;


use App\Entities\Quiz;
use Doctrine\ORM\EntityRepository;

class QuestionTagRepository extends EntityRepository
{
    public function getUniqueTagsForQuiz(Quiz $quiz)
    {
        $qb = $this->createQueryBuilder('t');
        return $qb->leftJoin('t.questions', 'q')
            ->where($qb->expr()->eq('q.quiz', ':quiz'))
            ->setParameter('quiz', $quiz)
            ->getQuery()
            ->getResult();
    }

}