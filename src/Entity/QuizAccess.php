<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Carbon\Carbon;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;

/**
 * @ORM\Entity()
 * @ORM\Table(name="quiz_access")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="App\Repository\QuizAccessRepository")
 * @ApiResource(
 *     collectionOperations={
 *          "get" = {
 *               "normalization_context"={"groups" = {"quiz-access:get"}}
 *          },
 *          "post" = {
 *               "access_control"="is_granted('ROLE_ADMIN')",
 *               "normalization_context"={"groups" = {"quiz-access:post"}}
 *          }
 *     },
 *     itemOperations={
 *          "get" = {
 *              "normalization_context"={"groups" = {"quiz-access:get","item:quiz-access:get"}}
 *          },
 *          "put" = {
 *               "access_control"="is_granted('ROLE_ADMIN')",
 *               "normalization_context"={"groups" = {"quiz-access:put"}}
 *          },
 *          "delete" = {
 *               "access_control"="is_granted('ROLE_ADMIN')"
 *          }
 *     }
 * )
 * @ApiFilter(SearchFilter::class, properties={"id":"exact", "quiz":"exact", "classroom":"exact"})
 * @ApiFilter(ExistsFilter::class,properties={"deletedAt"})
 * @ApiFilter(OrderFilter::class,properties= {"name","createdAt","updatedAt"},  arguments={"orderParameterName"="order"})
 *
 *
 */
class QuizAccess
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @Groups({"classroom:get","quiz-access:get"})
     */
    protected $id;

    /**
     * @var bool
     * @ORM\Column(name="is_hidden",type="boolean",nullable=true)
     * @Groups({"classroom:get","quiz-access:get","quiz-access:post","quiz-access:put"})
     */
    protected $isHidden;

    /**
     * @var int
     * @ORM\Column(name="num_attempts",type="integer",nullable=true)
     * @Groups({"classroom:get","quiz-access:get","quiz-access:post","quiz-access:put"})
     */
    protected $numAttempts;

    /**
     * @var \DateTime
     * @ORM\Column(name="open_date",type="datetime",nullable=true)
     * @Groups({"classroom:get","quiz-access:get","quiz-access:post","quiz-access:put"})
     */
    protected $openDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="close_date",type="datetime",nullable=true)
     * @Groups({"classroom:get","quiz-access:get","quiz-access:post","quiz-access:put"})
     */
    protected $closeDate;

    /**
     * One Product has One Shipment.
     *
     * @ORM\ManyToOne(targetEntity="Quiz",inversedBy="access")
     * @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     * @Groups({"classroom:get","quiz-access:get","quiz-access:post","quiz-access:put"})
     */
    protected $quiz;

    /**
     * One Classroom has many quizzes.
     *
     * @ORM\ManyToOne(targetEntity="Classroom",inversedBy="quizAccess")
     * @ORM\JoinColumn(name="classroom_id", referencedColumnName="id")
     * @Groups({"quiz-access:get","quiz-access:post"})
     */
    protected $classroom;

    /**
     * @ORM\OneToMany(targetEntity="QuizSession",mappedBy="quizAccess")
     */
    protected $quizSessions;


    /**
     * @return Classroom
     */
    public function getClassroom(): Classroom
    {
        return $this->classroom;

    }

    /**
     * @param mixed $classroom
     *
     * @return QuizAccess
     */
    public function setClassroom($classroom): QuizAccess
    {
        $this->classroom = $classroom;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getQuizSessions(): Collection
    {
        return $this->quizSessions;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param \DateTime $openDate
     *
     * @return QuizAccess
     */
    public function setOpenDate(\DateTime $openDate): QuizAccess
    {
        $this->openDate = $openDate;

        return $this;
    }

    /**
     * @param \DateTime $closeDate
     *
     * @return QuizAccess
     */
    public function setCloseDate(\DateTime $closeDate): QuizAccess
    {
        $this->closeDate = $closeDate;

        return $this;
    }

    /**
     * @param bool $isHidden
     */
    public function setIsHidden(bool $isHidden): QuizAccess
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsHidden(): bool
    {
        return $this->isHidden;
    }

    /**
     * @param mixed $quiz
     *
     * @return QuizAccess
     */
    public function setQuiz($quiz): QuizAccess
    {
        $this->quiz = $quiz;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCloseDate(): Carbon
    {
        return Carbon::instance($this->closeDate);
    }

    /**
     * @return \DateTime
     */
    public function getOpenDate(): Carbon
    {
        return Carbon::instance($this->openDate);
    }

    /**
     * @param int $numAttempts
     *
     * @return QuizAccess
     */
    public function setNumAttempts(int $numAttempts): QuizAccess
    {
        $this->numAttempts = $numAttempts;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }

    /**
     * @return int
     */
    public function getNumAttempts(): int
    {
        return $this->numAttempts;
    }

    public function isOpen($user)
    {
        if ($this->isHidden == true)
            return false;

        if (Carbon::today() > $this->closeDate) {
            return false;
        }
        if (Carbon::today() < $this->openDate) {
            return false;
        }
        // number of user attempts exceeds the max attempts on a quiz
        if ($this->getQuiz()->getQuizSessionsByAccessAndUser($user,$this)->count() >= $this->numAttempts) {
            return false;
        }

        return true;
    }
}
