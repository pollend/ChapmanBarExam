<?php

namespace App\Repository;

use App\Entity\Classroom;
use App\Entity\Quiz;
use App\Entity\QuizQuestion;
use App\Entity\QuizSession;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class QuizResponseRepository extends EntityRepository
{
    public function filterResponsesNotInQuestions(array $questions, $type = null)
    {
        $qb = $this->createQueryBuilder('r');

        $builder = $qb->where($qb->expr()->notIn('question', ':question'))
            ->setParameter('question', $questions);
        if ($type) {
            $builder->andWhere($qb->expr()->isInstanceOf('r.type', ':type'))
                ->setParameter('type', $type);
        }

        return $builder->getQuery()->getResult();
    }

    public function filterResponsesByQuestions(array $questions, $type = null)
    {
        $qb = $this->createQueryBuilder('r');

        $builder = $qb->where($qb->expr()->in('question', ':question'))
            ->setParameter('question', $questions);
        if ($type) {
            $builder->andWhere($qb->expr()->isInstanceOf('r.type', ':type'))
                ->setParameter('type', $type);
        }

        return $builder->getQuery()->getResult();
    }

    /**
     * @param QuizSession $session
     *
     * @return ArrayCollection
     */
    public function bySession(QuizSession $session)
    {
        $qb = $this->createQueryBuilder('r');

        return $qb->where($qb->expr()->eq('r.session', ':session'))
            ->setParameter('session', $session)
            ->getQuery()
            ->getResult();
    }

    public function filterByClassQuizAndQuestion(Classroom $classroom, Quiz $quiz,QuizQuestion $question){
        $qb = $this->createQueryBuilder('r');
        return $qb->where($qb->expr()->eq('r.question',':question'))
            ->join('r.session','s')
            ->andWhere($qb->expr()->eq('s.quiz',':quiz'))
            ->andWhere($qb->expr()->eq('s.classroom',':classroom'))
            ->setParameters(['question' => $question,'quiz' => $quiz,'classroom' => $classroom])
            ->getQuery()
            ->getResult();
    }

    /**
     * @param QuizSession $session
     * @param $questions
     *
     * @return ArrayCollection
     */
    public function filterResponsesBySessionAndQuestions(QuizSession $session, $questions)
    {
        $qb = $this->createQueryBuilder('r');

        return $qb->where($qb->expr()->eq('r.session', ':session'))
            ->andWhere($qb->expr()->in('r.question', ':questions'))
            ->setParameter('session', $session)
            ->setParameter('questions', $questions)
            ->getQuery()
            ->getResult();
    }

    public function filterByQuestionAndSessions(QuizQuestion $question,$sessions){

        $qb = $this->createQueryBuilder('r');
        return $qb->where($qb->expr()->in('r.session', ':sessions'))
            ->andWhere($qb->expr()->eq('r.question', ':questions'))
            ->setParameter('sessions', $sessions)
            ->setParameter('questions', $question)
            ->getQuery()
            ->getResult();
    }

    public function filterResponseBySessionAndQuestion(QuizSession $session, QuizQuestion $question)
    {
        $qb = $this->createQueryBuilder('r');
        return $qb->where($qb->expr()->eq('r.session', ':session'))
            ->andWhere($qb->expr()->eq('r.question', ':questions'))
            ->setParameter('session', $session)
            ->setParameter('questions', $question)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
