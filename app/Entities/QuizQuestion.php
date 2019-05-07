<?php

namespace App\Entities;
use App\Entities\Quiz;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repositories\QuestionRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
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

    function getOrder(){
        return $this->order;
    }

    /**
     * @param $order
     * @return $this
     */
    public function setOrder($order){
        $this->order = $order;
        return $this;
    }

    /**
     * @param $group
     * @return $this
     */
    public function setGroup($group){
        $this->group = $group;
        return $this;
    }

    public function getGroup(){
        return $this->group;
    }
}