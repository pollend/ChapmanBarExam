<?php


namespace App\Entities;

use Carbon\Carbon;

use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @package App\Entities
 *
 * @ORM\Entity()
 * @ORM\Table(name="quiz_access")
 * @ORM\HasLifecycleCallbacks
 *
 */
class QuizAccess
{
    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @JMS\Groups({"list"})
     * @JMS\Type("int")
     */
    protected $id;

    /**
     * @var bool
     * @ORM\Column(name="is_hidden",type="boolean",nullable=true)
     * @JMS\Groups({"list"})
     */
    protected $isHidden;

    /**
     * @var integer
     * @ORM\Column(name="num_attempts",type="integer",nullable=true)
     * @JMS\Groups({"list"})
     */
    protected $numAttempts;


    /**
     * @var \DateTime
     * @ORM\Column(name="open_date",type="datetime",nullable=true)
     * @JMS\Groups({"list"})
     */
    protected $openDate;


    /**
     * @var \DateTime
     * @ORM\Column(name="close_date",type="datetime",nullable=true)
     * @JMS\Groups({"list"})
     */
    protected $closeDate;

    /**
     * One Product has One Shipment.
     * @ORM\ManyToOne(targetEntity="Quiz",inversedBy="access")
     * @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     */
    protected $quiz;

    /**
     * One Classroom has many quizzes.
     * @ORM\ManyToOne(targetEntity="Classroom",inversedBy="quizAccess")
     * @ORM\JoinColumn(name="classroom_id", referencedColumnName="id")
     */
    protected $classroom;


    /**
     * @return mixed
     */
    public function getClassroom()
    {
        return $this->classroom;
    }

    /**
     * @param mixed $classroom
     */
    public function setClassroom($classroom): void
    {
        $this->classroom = $classroom;
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
     */
    public function setOpenDate(\DateTime $openDate): void
    {
        $this->openDate = $openDate;
    }

    /**
     * @param \DateTime $closeDate
     */
    public function setCloseDate(\DateTime $closeDate): void
    {
        $this->closeDate = $closeDate;
    }

    /**
     * @param mixed $quiz
     */
    public function setQuiz($quiz): void
    {
        $this->quiz = $quiz;
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
     */
    public function setNumAttempts(int $numAttempts): void
    {
        $this->numAttempts = $numAttempts;
    }

    /**
     * @return mixed
     */
    public function getQuiz()
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

    public function isOpen($user){

        if (Carbon::today() > $this->closeDate)
            return false;
        if(Carbon::today() < $this->openDate)


//        // number of user attempts exceeds the max attempts on a quiz
        if ($this->getQuiz()->getQuizSessionsByUser($user)->count() >= $this->numAttempts)
            return false;

        return true;
    }

}