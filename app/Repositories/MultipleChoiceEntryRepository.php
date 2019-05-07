<?php


namespace App\Repositories;


use App\Entities\MultipleChoiceEntry;
use Doctrine\ORM\EntityRepository;

class MultipleChoiceEntryRepository extends EntityRepository
{
    /**
     * @param $question
     * @return array
     */
    public function getEntriesForQuestion($question)
    {
        $qb = $this->createQueryBuilder('e');
        return $qb->where($qb->expr()->eq('e.question', ':question'))
            ->setParameter('question', $question)
            ->getQuery()
            ->getResult();
    }

}