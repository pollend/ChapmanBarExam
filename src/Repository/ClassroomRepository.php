<?php

namespace App\Repository;

use App\Datatable;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;

class ClassroomRepository extends EntityRepository
{
    public function byUser(User $user)
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->innerJoin('c.users', 'u', 'WITH', $qb->expr()->eq('u.id', ':user'))
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function byEmail($email)
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->innerJoin('c.emailWhitelist', 'w', 'WITH', $qb->expr()->eq('w.email', ':email'))
            ->setParameter('email', $email)
            ->getQuery()
            ->getResult();
    }

    private function _filter(Request $request)
    {
        $qb = $this->createQueryBuilder('c');
        if ($name = $request->get('name', null)) {
            $qb->where($qb->expr()->like('c.name', ':name'))
                ->setParameter('name', '%'.$name.'%');
        }

        return $qb;
    }

    public function filter(Request $request)
    {
        return $this->_filter($request)->getQuery();
    }

    public function dataTable(Request $request)
    {
        $qb = $this->_filter($request);
        $dataTable = new DataTable();
        $dataTable->handleSort($request, ['name' => 'name', 'created_at' => 'createdAt', 'updated_at' => 'updatedAt'], function ($column, $sort) use ($qb) {
            $qb->orderBy('c.'.$column, $sort);
        });

        $paginator = $this->paginator(
            $qb->getQuery(),
            (int) $request->get('page', 0),
            (int) $request->get('pageSize', 10),
            200
        );
        $dataTable->setPayload($paginator);

        return $dataTable;
    }

    /**
     * @param Query $query
     * @param int   $page
     * @param int   $perPage
     * @param int   $limit
     *
     * @return Paginator
     */
    public function paginator(Query $query, $page, $perPage, $limit = 10)
    {
        $pagination = new Paginator($query);
        $num = $perPage < $limit ? $perPage : $limit;
        $pagination->getQuery()->setMaxResults($num);
        $pagination->getQuery()->setFirstResult($num * $page);

        return $pagination;
    }
}
