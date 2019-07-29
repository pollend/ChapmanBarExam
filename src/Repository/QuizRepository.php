<?php

namespace App\Repository;

use App\Datatable;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

class QuizRepository extends EntityRepository
{
    public function byName($name){
        $qb = $this->createQueryBuilder('q');
        try {
            $result = $qb->where($qb->expr()->like('q.name', ':name'))
                ->setParameter('name', $name)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $exception) {
            return null;
        }
        return $result;
    }

    private function _filter(Request $request)
    {
        $qb = $this->createQueryBuilder('q');
        if ($name = $request->get('name', null)) {
            $qb->where($qb->expr()->like('q.name', ':name'))
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
            $qb->orderBy('q.'.$column, $sort);
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
