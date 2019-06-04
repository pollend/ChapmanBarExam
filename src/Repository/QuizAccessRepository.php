<?php


namespace App\Repository;

use App\Entity\Classroom;
use App\Entity\Quiz;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class QuizAccessRepository extends EntityRepository
{

    /**
     * @param Classroom $classroom
     * @param Quiz $quiz
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getAccessByClass(Classroom $classroom, Quiz $quiz)
    {
        $qb = $this->createQueryBuilder('q');
        return $qb->where($qb->expr()->eq('q.quiz', ':quiz'))
            ->andWhere($qb->expr()->eq('q.classroom', ':classroom'))
            ->setParameters(['quiz' => $quiz, 'classroom' => $classroom])
            ->getQuery()
            ->getSingleResult();
    }

}