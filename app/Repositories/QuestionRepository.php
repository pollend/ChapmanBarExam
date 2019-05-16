<?php


namespace App\Repositories;


use App\Entities\QuizQuestion;
use Doctrine\ORM\EntityRepository;
use Illuminate\Support\Collection;

class QuestionRepository extends EntityRepository
{
    public function findByType($type,$quiz)
    {
        $qb = $this->createQueryBuilder('q');
        return $qb->where($qb->expr()->isInstanceOf('e.type', ':type'))
            ->andWhere($qb->expr()->eq('q.quiz',':quiz'))
            ->setParameter('type', $type)
            ->setParameter('quiz',$quiz)
            ->getQuery()
            ->getResult();
    }


    public function filterByGroup($group,$quiz){
        $qb = $this->createQueryBuilder('q');
        return $qb->where($qb->expr()->eq('q.group',':group'))
            ->andWhere($qb->expr()->eq('q.quiz',':quiz'))
            ->orderBy($qb->expr()->asc('q.order'))
            ->setParameter('group',$group)
            ->setParameter('quiz',$quiz)
            ->getQuery()
            ->getResult();
    }

//    public function applyGrouping($questions,$reindex = false){
//        $collection = Collection::make($questions)
//            ->sortBy(function ($q){return $q->getGroup();})
//            ->groupBy(function ($q){return $q->getGroup();})
//            ->transform(function ($entry) {
//                return $entry->sortby(function ($o){return $o->getOrder();})->values();
//            });
//        if($reindex == true)
//            $collection->values();
//        return $collection;
//    }

    public function getUniqueGroups($quiz){
        $qb = $this->createQueryBuilder('q');
        $groups =  $qb->select('q.group')
            ->where($qb->expr()->eq('q.quiz',':quiz'))
            ->setParameter('quiz',$quiz)
            ->distinct()
            ->getQuery()
            ->getScalarResult();
        return Collection::make($groups)
            ->sortBy('group')
            ->pluck('group');
    }

    public function getUniqueOrder($quiz){
        $qb = $this->createQueryBuilder('q');
        $orders = $qb->select('q.order')
            ->where($qb->expr()->eq('q.quiz',':quiz'))
            ->setParameter('quiz',$quiz)
            ->distinct()
            ->getQuery()
            ->getScalarResult();
        return Collection::make($orders)
            ->sortBy('order')
            ->pluck('order');

    }
}