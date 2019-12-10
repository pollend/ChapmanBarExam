<?php


namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class UserWhitelistRepository extends EntityRepository
{
    public function byEmail(String $email,$classroom){
        $qb = $this->createQueryBuilder('e');
        return $qb->where($qb->expr()->eq('e.email',':email'))
            ->andWhere($qb->expr()->eq('e.classroom',':classroom'))
            ->setParameter('email',$email)
            ->setParameter('classroom',$classroom)
            ->getQuery()
            ->getSingleResult();

    }
}
