<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\TimestampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\Table(name="quiz_question")
 *
 * @DiscriminatorMap(typeProperty = "type", mapping = {
 *    "multipleChoice": "App\Entity\MultipleChoiceQuestion",
 *    "shortAnswer": "App\Entity\ShortAnswerQuestion",
 *    "textBlock": "App\Entity\TextBlockQuestion"
 * })
 * @ApiResource(
 *     collectionOperations={
 *          "get_questions_by_session" = {
 *              "method"="GET",
 *              "access_control"="is_granted('ROLE_USER')",
 *              "controller"=App\Controller\GetQuizQuestionBySession::class,
 *              "path" = "/questions/sessions/{session_id}",
 *              "normalization_context"={"groups"={"quiz_question:get"}}
 *          },
 *          "get_questions_by_session_page" = {
 *              "method"="GET",
 *              "access_control"="is_granted('ROLE_USER')",
 *              "controller"=App\Controller\GetQuizQuestionBySessionAndPage::class,
 *              "path" = "/questions/sessions/{session_id}/page/{page}",
 *              "normalization_context"={"groups"={"quiz_question:get"}}
 *          },
 *          "get",
 *          "post"
 *     }
 * )
 */
abstract class QuizQuestion
{
    use TimestampTrait;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @Groups({"quiz_question:get"})
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(name="`order`",type="integer",nullable=false)
     * @ORM\OrderBy({"name" = "ASC"})
     * @Groups({"quiz_question:get"})
     */
    protected $order;

    /**
     * @var int
     * @ORM\Column(name="`group`",type="integer",nullable=false)
     * @ORM\OrderBy({"name" = "ASC"})
     * @Groups({"quiz_question:get"})
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
     * Many Groups have Many Users.
     *
     * @ORM\ManyToMany(targetEntity="QuestionTag", inversedBy="questions")
     *
     * @var PersistentCollection
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

    public function getOrder()
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
     *
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @param Quiz $quiz
     *
     * @return QuizQuestion
     */
    public function setQuiz(Quiz $quiz): QuizQuestion
    {
        $this->quiz = $quiz;

        return $this;
    }

    /**
     * @return Quiz
     */
    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }

    /**
     * @param $group
     *
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
