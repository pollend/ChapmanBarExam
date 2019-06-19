<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class QuizSessionRepository extends EntityRepository
{
    /**
     * @deprecated
     * @param User $user
     * @return mixed|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getActiveSession(User $user)
    {
        $qb = $this->createQueryBuilder('q');

        if ($sessions = $qb->where($qb->expr()->eq('q.owner', ':owner'))
            ->andWhere($qb->expr()->isNull('q.submittedAt'))
            ->setParameter('owner', $user)
            ->getQuery()
            ->getSingleResult()) {
            return $sessions;
        }

        return null;
    }

    public function getActiveSessions(User $user)
    {
        $qb = $this->createQueryBuilder('q');

        if ($sessions = $qb->where($qb->expr()->eq('q.owner', ':owner'))
            ->andWhere($qb->expr()->isNull('q.submittedAt'))
            ->setParameter('owner', $user)
            ->getQuery()
            ->getResult()) {
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
