<?php


namespace App\Repository;



use App\Entity\Classroom;
use App\Entity\User;
use App\Entity\UserWhitelist;
use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Illuminate\Support\Collection;

class ClassroomRepository extends EntityRepository
{

    public function byUser(User $user)
    {
        $qb = $this->createQueryBuilder('c');
        return Collection::make($qb->innerJoin('c.users', 'u', 'WITH', $qb->expr()->eq('u.id', ':user'))
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult())->keyBy(function ($c) {

            return $c->getId();
        });
    }

    public function byEmail($email)
    {
        $qb = $this->createQueryBuilder('c');
        return Collection::make($qb
            ->innerJoin('c.whitelists', 'w', 'WITH', $qb->expr()->eq('w.email', ':email'))
            ->setParameter('email', $email)
            ->getQuery()
            ->getResult())->keyBy(function ($c) {
            return $c->getId();
        });
    }

//    public function getClassroomsForUser(User $user) : Collection
//    {
//        $em = $this->getEntityManager();
//
//        $qb = $this->createQueryBuilder('c');
//        $c1 = Collection::make($qb
//            ->innerJoin('c.whitelists', 'w', 'WITH', $qb->expr()->eq('w.email', ':email'))
//            ->setParameter('email', $user->getEmail())
//            ->getQuery()
//            ->getResult())->keyBy(function ($c) {
//            return $c->getId();
//        });
//        $c2 = $this->byClassroom($user);
//
//        if ($c1->count() > 0) {
//            // look for users not already in the user list
//            /** @var Classroom $c */
//            foreach ($c1 as $c) {
//                if (!$c2->contains($c)) {
//                    $c->getUsers()->add($user);
//                }
//            }
//            // clear emails from whitelist and wipe
//            /** @var EntityRepository $e */
//            $whiteListRepository = $em->getRepository(UserWhitelist::class);
//            foreach ($whiteListRepository->findBy(["email" => $user->getEmail()]) as $w) {
//                $em->remove($w);
//            }
//
//            // save users added to class
//            $em->persist($c);
//            $em->flush();
//        } else {
//            return $c2->values();
//        }
//        return $this->byClassroom($user);
//    }

}