<?php

namespace App\Repository;

use App\Entity\Classroom;
use App\Entity\Quiz;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class QuizSessionRepository extends EntityRepository
{

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

    public function getSessionsByClassAndQuiz(Quiz $quiz, Classroom $classroom){
        $qb = $this->createQueryBuilder('q');
        return $qb->where($qb->expr()->eq('q.classroom',':classroom'))
            ->andWhere($qb->expr()->eq('q.quiz',':quiz'))
            ->setParameters(['classroom' => $classroom, 'quiz' => $quiz])
            ->getQuery()
            ->getResult();

    }

}
