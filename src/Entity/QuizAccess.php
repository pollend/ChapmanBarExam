<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 * @ORM\Table(name="quiz_access")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="App\Repository\QuizAccessRepository")
 * @ApiResource()
 */
class QuizAccess
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @Groups({"list"})
     */
    protected $id;

    /**
     * @var bool
     * @ORM\Column(name="is_hidden",type="boolean",nullable=true)
     * @Groups({"list"})
     */
    protected $isHidden;

    /**
     * @var int
     * @ORM\Column(name="num_attempts",type="integer",nullable=true)
     * @Groups({"list"})
     */
    protected $numAttempts;

    /**
     * @var \DateTime
     * @ORM\Column(name="open_date",type="datetime",nullable=true)
     * @Groups({"list"})
     */
    protected $openDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="close_date",type="datetime",nullable=true)
     * @Groups({"list"})
     */
    protected $closeDate;

    /**
     * One Product has One Shipment.
     *
     * @ORM\ManyToOne(targetEntity="Quiz",inversedBy="access")
     * @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     * @Groups({"access_quiz"})
     */
    protected $quiz;

    /**
     * One Classroom has many quizzes.
     *
     * @ORM\ManyToOne(targetEntity="Classroom",inversedBy="quizAccess")
     * @ORM\JoinColumn(name="classroom_id", referencedColumnName="id")
     */
    protected $classroom;

    /**
     * @ORM\OneToMany(targetEntity="QuizSession",mappedBy="quizAccess")
     */
    protected $quizSessions;

    /**
     * @return Classroom
     */
    public function getClassroom() : Classroom
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
    public function isHidden(): bool
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
    public function getQuiz() : Quiz
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
        if (Carbon::today() > $this->closeDate) {
            return false;
        }
        if (Carbon::today() < $this->openDate) {
//        // number of user attempts exceeds the max attempts on a quiz
            if ($this->getQuiz()->getQuizSessionsByUser($user)->count() >= $this->numAttempts) {
                return false;
            }
        }

        return true;
    }
}
