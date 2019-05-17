<?php

namespace App\Entities;
use App\Entities\Quiz;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repositories\QuestionRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\Table(name="quiz_question")
 */
class QuizQuestion
{



        /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     */
    protected $id;

    /**
     * @var integer
     * @ORM\Column(name="`order`",type="integer",nullable=false)
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected $order;

    /**
     * @var integer
     * @ORM\Column(name="`group`",type="integer",nullable=false)
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected $group;

    /**
     * @var Quiz
     * @ORM\ManyToOne(targetEntity="Quiz",inversedBy="questions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     * })
     */
    protected $quiz;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="QuizResponse",mappedBy="question")
     */
    protected $responses;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    function getOrder()
    {
        return $this->order;
    }

    public function answers()
    {
        return $this->responses;
    }

    public function answersBySession($session)
    {
        return $this->responses->matching(Criteria::create()->where(Criteria::expr()->eq('session', $session)));
    }

    /**
     * @param $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @param $group
     * @return $this
     */
    public function setGroup($group)
    {
        $this->group = $group;
        return $this;
    }

    public function getGroup()
    {
        return $this->group;
    }

}