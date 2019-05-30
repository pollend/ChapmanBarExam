<?php


namespace App\Repositories;


use App\Entities\Classroom;
use App\Entities\User;
use App\Entities\UserWhitelist;
use App\Utility\Datatable;
use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ClassroomRepository extends EntityRepository
{

    private function byClassroom(User $user)
    {
        $qb = $this->createQueryBuilder('c');
        return Collection::make($qb->innerJoin('c.users', 'u', 'WITH', $qb->expr()->eq('u.id', ':user'))
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult())->keyBy(function ($c) {

            return $c->getId();
        });
    }

    public function getClassroomsForUser(User $user) : Collection
    {
        $qb = $this->createQueryBuilder('c');
        $c1 = Collection::make($qb
            ->innerJoin('c.whitelists', 'w', 'WITH', $qb->expr()->eq('w.email', ':email'))
            ->setParameter('email', $user->getEmail())
            ->getQuery()
            ->getResult())->keyBy(function ($c) {
            return $c->getId();
        });
        $c2 = $this->byClassroom($user);

        if ($c1->count() > 0) {
            // look for users not already in the user list
            /** @var Classroom $c */
            foreach ($c1 as $c) {
                if (!$c2->contains($c)) {
                    $c->getUsers()->add($user);
                }
            }
            // clear emails from whitelist and wipe
            /** @var EntityRepository $e */
            $whiteListRepository = \EntityManager::getRepository(UserWhitelist::class);
            foreach ($whiteListRepository->findBy(["email" => $user->getEmail()]) as $w) {
                \EntityManager::remove($w);
            }

            // save users added to class
            \EntityManager::persist($c);
            \EntityManager::flush();
        } else {
            return $c2->values();
        }
        return $this->byClassroom($user);
    }

    public function datatable(Request $request){
        $datatable = new Datatable();
        $datatable->handleSort($request);

    }
}