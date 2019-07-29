<?php

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\NoResultException;

class UserRepository extends EntityRepository
{
    public function findByEmail($email)
    {
        $qb = $this->createQueryBuilder('u');
        try {
            return $qb->where($qb->expr()->eq('u.email', ':email'))
                ->setParameter('email', $email)
                ->getQuery()
                ->getSingleResult();
        }catch (NoResultException $e){
            return null;
        }

    }
}
