<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\SoftDeleteTrait;
use App\Entity\Traits\TimestampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
//TODO: add Delete for quiz
//TODO: work out access for user

/**
 * Class Quiz.
 *
 * @ORM\Entity(repositoryClass="App\Repository\QuizRepository")
 * @ORM\Table(name="quiz")
 * @ORM\HasLifecycleCallbacks
 * @ApiResource(
 *     itemOperations={
 *          "get" = {
 *               "access_control"="is_granted('ROLE_ADMIN')",
 *               "normalization_context"={"groups"={"quiz:get","timestamp"}},
 *          },
 *          "put" = {
 *               "access_control"="is_granted('ROLE_ADMIN')"
 *          }
 *     },
 *     collectionOperations={
 *          "get" = {
 *               "access_control"="is_granted('ROLE_ADMIN')",
 *               "normalization_context"={"groups"={"quizes:get","timestamp"}},
 *          },
 *          "post" = {
 *               "access_control"="is_granted('ROLE_ADMIN')"
 *          }
 *      }
 * )
 *
 * @ApiFilter(OrderFilter::class,properties={"createdAt", "updatedAt", "name"}, arguments={"orderParameterName"="order"})
 * @ApiFilter(SearchFilter::class,properties={"id":"exact", "name":"partial", "description":"partial","quizSessions.classroom":"exact"})
 */
class Quiz
{
    use TimestampTrait;
    use SoftDeleteTrait;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @Groups({"classroom:get","quiz_session:get","quiz-access:get","quiz:get","quizes:get"})
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="name",type="string",length=50,nullable=false)
     * @Groups({"classroom:get","quiz_session:get","quiz-access:get","quiz:get","quizes:get"})
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="description",type="text",nullable=false)
     * @Groups({"classroom:get","quiz-access:get","quiz:get","quizes:get"})
     */
    protected $description;

    /**
     * @var PersistentCollection
     * @ORM\OneToMany(targetEntity="QuizSession",mappedBy="quiz")
     * @Groups({"quiz_sessions"})
     */
    protected $quizSessions;

    /**
     * @var PersistentCollection
     * @ORM\OneToMany(targetEntity="QuizQuestion",mappedBy="quiz")
     * @Groups({"quiz_questions","quiz:get"})
     */
    protected $questions;

    /**
     * One product has many features. This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="QuizAccess", mappedBy="quiz")
     */
    protected $access;

    /**
     * @var
     * @ORM\Column(name="max_score",type="integer",nullable=true)
     */
    protected $maxScore;


    /**
     * @return mixed
     */
    public function getMaxScore()
    {
        return $this->maxScore;
    }

    /**
     * @param mixed $maxScore
     */
    public function setMaxScore($maxScore): void
    {
        $this->maxScore = $maxScore;
    }

    /**
     * @Groups({"list","detail"})
     **/
    public function getNumQuestions()
    {
        return $this->getQuestions()->count();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): Quiz
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): Quiz
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getQuestions(): PersistentCollection
    {
        return $this->questions;
    }

    /**
     * @return PersistentCollection
     */
    public function getQuizSessions(): PersistentCollection
    {
        return $this->quizSessions;
    }

    /**
     * @param $user
     *
     * @return ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getQuizSessionsByUser($user)
    {
        return $this->quizSessions->matching(Criteria::create()->where(Criteria::expr()->eq('owner', $user)));
    }
}
