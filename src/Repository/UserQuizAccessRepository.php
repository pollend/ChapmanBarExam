<?php


namespace App\Repository;


use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserQuizAccessRepository extends EntityRepository
{

    public function byOwner(User $owner){
        $qb = $this->createQueryBuilder('q');
        $qb->where($qb->expr()->eq("owner",":owner"))
            ->setParameter("owner", $owner)
            ->getQuery()
            ->getResult();
    }
}