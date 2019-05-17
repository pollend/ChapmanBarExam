<?php


namespace App\Repositories;


use App\Entities\MultipleChoiceResponse;
use App\Entities\QuizSession;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

class QuizResponseRepository extends EntityRepository
{
    public function findCorrectResponses(QuizSession $session){
        $qb = $this->createQueryBuilder('r');
        $qb->where($qb->expr()->isInstanceOf('r.type', ':type'))
            ->leftJoin('r.question','p','WITH',$qb->expr()->eq('p.correctEntry','r.choice'))
            ->setParameters(['type' => MultipleChoiceResponse::class])
            ->getQuery()
            ->getResult();
    }

    public function getCorrectEntries(QuizSession $session)
    {
        $qb = $this->createQueryBuilder('r');
        return $qb->leftJoin('r.question', 'p', 'WITH', $qb->expr()->eq('p.correctEntry', 'r.choice'))
            ->setParameters(['type' => MultipleChoiceResponse::class])
            ->getQuery()
            ->getResult();
    }


}