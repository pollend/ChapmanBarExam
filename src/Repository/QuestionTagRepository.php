<?php


namespace App\Repository;


use App\Entity\Quiz;
use Doctrine\Common\Collections\ArrayCollection;
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

    /**
     * @param $text
     * @param int $limit
     * @return ArrayCollection
     */
    public function filter($text,$limit = 10)
    {
        $qb = $this->createQueryBuilder('t');
        return $qb->where($qb->expr()->like('t.name', ":name"))
            ->setMaxResults($limit)
            ->setParameter('name', '%' . $text . '%')
            ->getQuery()
            ->getResult();
    }

}