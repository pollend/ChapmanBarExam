<?php


namespace App\Repository;


use App\Entity\QuizSession;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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


    /**
     * @param QuizSession $session
     * @return ArrayCollection
     */
    public function filterResponsesBySession(QuizSession $session){
        $qb = $this->createQueryBuilder('r');
        return $qb->where($qb->expr()->eq('r.session', ':session'))
            ->setParameter('session',$session)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param QuizSession $session
     * @param $questions
     * @return ArrayCollection
     */
    public function filterResponsesBySessionAndQuestions(QuizSession $session, $questions){
        $qb = $this->createQueryBuilder('r');
        return $qb->where($qb->expr()->eq('r.session', ':session'))
            ->andWhere($qb->expr()->in('r.question',':questions'))
            ->setParameter('session',$session)
            ->setParameter('questions',$questions)
            ->getQuery()
            ->getResult();
    }

    public function filterResponseBySessionAndQuestion(QuizSession $session, $question){
        $qb = $this->createQueryBuilder('r');
        return $qb->where($qb->expr()->eq('r.session', ':session'))
            ->andWhere($qb->expr()->eq('r.question',':questions'))
            ->setParameter('session',$session)
            ->setParameter('questions',$question)
            ->getQuery()
            ->getOneOrNullResult();
    }
}