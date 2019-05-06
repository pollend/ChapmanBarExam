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
    public function startSession($user, $quiz)
    {

        if ($this->getActiveSession($user) !== null)
            throw new SessionInProgressException();

        if (!$this->quizRepository->isOpen($quiz, $user))
            throw new QuizClosedException();

        $session = new QuizSession();
        $session->quiz_id = $quiz->id;
        $session->owner_id = $user->id;
        $session->saveOrFail();
        return $session;
    }

    public function getActiveSession(User $user){
        $qb = $this->createQueryBuilder('q');
        return $qb
            ->where($qb->expr()->eq('q.owner', ':owner'))
            ->andWhere($qb->expr()->isNull('q.submittedAt'))
            ->setParameter('owner', $user)
            ->getQuery()
            ->getFirstResult();

    }
}