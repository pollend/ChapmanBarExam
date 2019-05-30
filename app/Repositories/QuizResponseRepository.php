<?php


namespace App\Repositories;


use App\Entities\MultipleChoiceResponse;
use App\Entities\QuizQuestion;
use App\Entities\QuizSession;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

class QuizResponseRepository extends EntityRepository
{
    public function filterResponsesNotInQuestions(array $questions, $type = null){
        $qb = $this->createQueryBuilder('r');

        $builder = $qb->where($qb->expr()->notIn('question',':question'))
            ->setParameter('question',$questions);
        if($type)
            $builder->andWhere($qb->expr()->isInstanceOf('r.type',':type'))
                ->setParameter('type',$type);
        return $builder->getQuery()->getResult();
    }

    public function filterResponsesByQuestions(array $questions, $type = null){
        $qb = $this->createQueryBuilder('r');

        $builder = $qb->where($qb->expr()->in('question',':question'))
            ->setParameter('question',$questions);
        if($type)
            $builder->andWhere($qb->expr()->isInstanceOf('r.type',':type'))
                ->setParameter('type',$type);
        return $builder->getQuery()->getResult();
    }

    public function filterResponsesBySession(QuizSession $session){
        $qb = $this->createQueryBuilder('r');
        return $qb->where($qb->expr()->eq('r.session', ':session'))
            ->setParameter('session',$session)
            ->getQuery()
            ->getResult();
    }

    public function filterResponsesBySessionAndQuestions(QuizSession $session, $questions){
        $qb = $this->createQueryBuilder('r');
        return $qb->where($qb->expr()->eq('r.session', ':session'))
            ->andWhere($qb->expr()->in('r.question',':questions'))
            ->setParameter('session',$session)
            ->setParameter('questions',$questions)
            ->getQuery()
            ->getResult();
    }
}