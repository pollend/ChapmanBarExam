<?php


namespace App\Repositories;


use App\Entities\User;
use App\Exceptions\QuizClosedException;
use App\Exceptions\SessionInProgressException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class QuizSessionRepository extends EntityRepository
{
    public function getActiveSession(User $user){
        $qb = $this->createQueryBuilder('q');

        if($session =  $qb->where($qb->expr()->eq('q.owner', ':owner'))
            ->andWhere($qb->expr()->isNull('q.submittedAt'))
            ->setParameter('owner', $user)
            ->getQuery()
            ->getOneOrNullResult()){
            \Debugbar::info('Found Active Session: ' . $session->getId());
            return $session;
        }
        return null;
    }

    public function getSessionsByUser(User $user){
        $qb = $this->createQueryBuilder('q');
        if($session =  $qb->where($qb->expr()->eq('q.owner', ':owner'))
            ->setParameter('owner', $user)
            ->getQuery()
            ->getResult()){
            return $session;
        }
        return null;
    }
}