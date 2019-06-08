<?php

namespace App\Entity;

use App\Entity\Traits\TimestampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation As JMS;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\Table(name="quiz_question")
 *
 * @JMS\Discriminator(field = "type", map = {
 *    "multiple_choice": "App\Entity\MultipleChoiceQuestion",
 *    "short_answer": "App\Entity\ShortAnswerQuestion",
 *    "text_block": "App\Entity\TextBlockQuestion"
 * },groups={"detail","list"})
 *
 */
abstract class QuizQuestion
{
    use TimestampTrait;

    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @JMS\Groups({"detail","list"})
     */
    protected $id;

    /**
     * @var integer
     * @ORM\Column(name="`order`",type="integer",nullable=false)
     * @ORM\OrderBy({"name" = "ASC"})
     * @JMS\Groups({"detail","list"})
     */
    protected $order;

    /**
     * @var integer
     * @ORM\Column(name="`group`",type="integer",nullable=false)
     * @ORM\OrderBy({"name" = "ASC"})
     * @JMS\Groups({"detail","list"})
     */
    protected $group;

    /**
     * @var Quiz
     * @ORM\ManyToOne(targetEntity="Quiz",inversedBy="questions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     * })
     * @JMS\Groups({"quiz"})
     */
    protected $quiz;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="QuizResponse",mappedBy="question")
     * @JMS\Groups({"responses"})
     */
    protected $responses;

    /**
     * Many Groups have Many Users.
     * @ORM\ManyToMany(targetEntity="QuestionTag", inversedBy="questions")
     * @var PersistentCollection
     * @JMS\Groups({"question_tags"})
     */
    private $tags;


    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }


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
     * @return Collection
     */
    public function getTags(): Collection
    {
        return $this->tags;
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
     * @param Quiz $quiz
     * @return QuizQuestion
     */
    public function setQuiz(Quiz $quiz): QuizQuestion
    {
        $this->quiz = $quiz;
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