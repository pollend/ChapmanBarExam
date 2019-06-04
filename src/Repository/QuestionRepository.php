<?php


namespace App\Repository;


use App\Entities\QuestionTag;
use App\Entities\Quiz;
use App\Entities\QuizQuestion;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\ORM\Query\AST\Join;
use Illuminate\Support\Collection;

class QuestionRepository extends EntityRepository
{
    public function findByType($type, $quiz)
    {
        $qb = $this->createQueryBuilder('q');
        return $qb->where($qb->expr()->isInstanceOf('e.type', ':type'))
            ->andWhere($qb->expr()->eq('q.quiz', ':quiz'))
            ->setParameter('type', $type)
            ->setParameter('quiz', $quiz)
            ->getQuery()
            ->getResult();
    }

    /**\
     * Filter questions by group
     * @param $group
     * @param $quiz
     * @return ArrayCollection
     */
    public function filterByGroup($group, $quiz)
    {
        $qb = $this->createQueryBuilder('q');
        return $qb->where($qb->expr()->eq('q.group', ':group'))
            ->andWhere($qb->expr()->eq('q.quiz', ':quiz'))
            ->orderBy($qb->expr()->asc('q.order'))
            ->setParameter('group', $group)
            ->setParameter('quiz', $quiz)
            ->getQuery()
            ->getResult();
    }

    /**
     * returns an array of the group values that are unique
     * @param $quiz
     * @return Collection
     */
    public function getUniqueGroups($quiz)
    {
        $qb = $this->createQueryBuilder('q');
        $groups = $qb->select('q.group')
            ->where($qb->expr()->eq('q.quiz', ':quiz'))
            ->setParameter('quiz', $quiz)
            ->distinct()
            ->getQuery()
            ->getScalarResult();
        return Collection::make($groups)
            ->sortBy('group')
            ->pluck('group');
    }

    /**
     * returns an array of the order values that are unique
     * @param $quiz
     * @return Collection
     */
    public function getUniqueOrder($quiz)
    {
        $qb = $this->createQueryBuilder('q');
        $orders = $qb->select('q.order')
            ->where($qb->expr()->eq('q.quiz', ':quiz'))
            ->setParameter('quiz', $quiz)
            ->distinct()
            ->getQuery()
            ->getScalarResult();
        return Collection::make($orders)
            ->sortBy('order')
            ->pluck('order');

    }

    public function filterQuestionsByResponses($responses, $type)
    {
        $qb = $this->createQueryBuilder('q');
        $query = $qb->where($qb->expr()->in('responses', ':resp'))
            ->setParameter('resp', $responses);
        if ($type)
            $query = $query->andWhere($qb->expr()->isInstanceOf('type', ':type'))
                ->setParameter('type', $type);
        return $query->getQuery()->getResult();
    }

    public function getQuestionsBySessions($session)
    {
        $qb = $this->createQueryBuilder('q');
        $query = $qb
            ->leftJoin('App\Entities\QuizResponse', 'r', \Doctrine\ORM\Query\Expr\Join::WITH, 'q = r.question')
            ->where($qb->expr()->eq('r.session', ':session'))
            ->setParameter('session', $session)
            ->orderBy('q.order', 'ASC')
            ->orderBy('q.group', 'ASC');
        return $query->getQuery()->getResult();
    }

    public function filterQuestionsByInResponses($quiz, $responses, $type = null)
    {
        $qb = $this->createQueryBuilder('q');
        $query = $qb
            ->leftJoin('App\Entities\QuizResponse', 'r', \Doctrine\ORM\Query\Expr\Join::WITH, 'q = r.question')
            ->where($qb->expr()->in('r.id', ':resp'))
            ->andWhere($qb->expr()->eq('q.quiz', ':quiz'))
            ->setParameter('resp', $responses)
            ->setParameter('quiz', $quiz);
        if ($type)
            $query->andWhere($qb->expr()->isInstanceOf('r', $type));
        return $query->getQuery()->getResult();
    }

    public function filterQuestionsByNotInResponses($quiz, $responses, $type = null)
    {
        $qb = $this->createQueryBuilder('q');
        $query = $qb
            ->leftJoin('App\Entities\QuizResponse', 'r', \Doctrine\ORM\Query\Expr\Join::WITH, 'q = r.question')
            ->where($qb->expr()->notIn('r.id', ':resp'))
            ->andWhere($qb->expr()->eq('q.quiz', ':quiz'))
            ->setParameter('resp', $responses)
            ->setParameter('quiz', $quiz);
        if ($type)
            $query->andWhere($qb->expr()->isInstanceOf('r', $type));
        return $query->getQuery()->getResult();
    }

    public function filterByTag(Quiz $quiz, QuestionTag $tag)
    {
        $qb = $this->createQueryBuilder('q');
        return $qb->leftJoin('q.tags', 't')
            ->where($qb->expr()->eq('t', ':tag'))
            ->andWhere($qb->expr()->eq('q.quiz', ':quiz'))
            ->setParameter('tag', $tag)
            ->setParameter('quiz', $quiz)
            ->getQuery()
            ->getResult();

    }
}