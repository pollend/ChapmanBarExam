<?php

namespace App\Repository;

use App\Entity\QuestionTag;
use App\Entity\Quiz;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

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

    public function byName($name)
    {
        $qb = $this->createQueryBuilder('t');
        try {
            $result = $qb->where($qb->expr()->like('t.name', ':name'))
                ->setParameter('name', $name)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $exception) {
            return null;
        }
        return $result;
    }

    /**
     * @param $text
     * @param int $limit
     *
     * @return ArrayCollection
     */
    public function filter($text, $limit = 10)
    {
        $qb = $this->createQueryBuilder('t');

        return $qb->where($qb->expr()->like('t.name', ':name'))
            ->setMaxResults($limit)
            ->setParameter('name', '%' . $text . '%')
            ->getQuery()
            ->getResult();
    }
}
