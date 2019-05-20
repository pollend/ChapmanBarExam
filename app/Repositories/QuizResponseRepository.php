<?php


namespace App\Repositories;


use App\Entities\MultipleChoiceResponse;
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

}