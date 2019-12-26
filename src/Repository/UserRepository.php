<?php

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\NoResultException;
use function Doctrine\ORM\QueryBuilder;

class UserRepository extends EntityRepository
{
    public function findByEmail($email)
    {
        $qb = $this->createQueryBuilder('u');
        try {
            return $qb->where($qb->expr()->eq('u.email', ':email'))
                ->setParameter('email', $email)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function filterByClassAndExam($classroom, $quiz) {
        $qb = $this->createQueryBuilder('u');
        return $qb->join('u.quizSessions','s')
            ->andWhere($qb->expr()->eq('s.quiz',':quiz'))
            ->andWhere($qb->expr()->eq('s.classroom',':classroom'))
            ->setParameters([
                'classroom' => $classroom,
                'quiz' => $quiz
            ])->getQuery()->getResult();
    }
}
