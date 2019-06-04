<?php


namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;


class QuizSessionRepository extends EntityRepository
{
    public function getActiveSession(User $user)
    {
        $qb = $this->createQueryBuilder('q');

        if ($sessions = $qb->where($qb->expr()->eq('q.owner', ':owner'))
            ->andWhere($qb->expr()->isNull('q.submittedAt'))
            ->setParameter('owner', $user)
            ->getQuery()
            ->getOneOrNullResult()) {
            return $sessions;
        }
        return null;
    }

    public function getSessionsByUser(User $user)
    {
        $qb = $this->createQueryBuilder('q');
        if ($session = $qb->where($qb->expr()->eq('q.owner', ':owner'))
            ->setParameter('owner', $user)
            ->getQuery()
            ->getResult()) {
            return $session;
        }
        return null;
    }
}