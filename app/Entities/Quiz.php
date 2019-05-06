<?php
namespace App\Entities;

use App\Entities\Traits\TimestampTrait;
use Carbon\Carbon;
use Carbon\Traits\Date;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Illuminate\Database\Eloquent\Model;
use Doctrine\ORM\Mapping AS ORM;

/**
 * Class Quiz
 * @package App
 *
 * @ORM\Entity(repositoryClass="App\Repositories\QuizRepository")
 * @ORM\Table(name="quiz")
 * @ORM\HasLifecycleCallbacks
 *
 */
class Quiz
{

    use TimestampTrait;

    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="name",type="string",length=50,nullable=false)
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="description",type="text",nullable=false)
     */
    protected $description;

    /**
     * @var \DateTime
     * @ORM\Column(name="close_date",type="datetime",nullable=true)
     */
    protected $closeDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="time_open",type="datetime",nullable=true)
     */
    protected $timeOpen;

    /**
     * @var string
     * @ORM\Column(name="is_open",type="boolean",nullable=true)
     */
    protected $isOpen;

    /**
     * @var string
     * @ORM\Column(name="is_hidden",type="boolean",nullable=true)
     */
    protected $isHidden;
    /**
     * @var integer
     * @ORM\Column(name="num_attempts",type="integer",nullable=true)
     */
    protected $numAttempts;

    /**
     * @var \DateTime
     * @ORM\Column(name="deleted_at",type="datetime",nullable=true)
     */
    protected $deletedAt;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="QuizSession",mappedBy="quiz")
     */
    protected $quizSessions;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="MultipleChoiceQuestion",mappedBy="quiz")
     */
    protected $multipleChoiceQuestions;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ShortAnswerQuestion",mappedBy="quiz")
     */
    protected $shortAnswerQuestions;


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
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return ArrayCollection
     */
    public function getMultipleChoiceQuestions(): ArrayCollection{
        return $this->multipleChoiceQuestions;
    }

    public function getShortAnswerQuestions():ArrayCollection{
        return $this->shortAnswerQuestions;
    }


    /**
     * @return int
     */
    public function getNumAttempts(): int
    {
        return $this->numAttempts;
    }

    public function getQuizSessionsByUser($user){
        return $this->quizSessions->matching(Criteria::create()->where(Criteria::expr()->eq('owner',$user)));
    }

    /**
     * @return \DateTime
     */
    public function getCloseDate(): \Carbon\Carbon
    {
        return Carbon::instance($this->closeDate);
    }

    public function isOpen($user){

        if (Carbon::today() > $this->closeDate)
            return false;

        if ($this->getQuizSessionsByUser($user)->count() < $this->numAttempts)
            return false;

        if($this->isOpen === false)
            return false;


        return true;
    }


}
