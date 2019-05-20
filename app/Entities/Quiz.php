<?php
namespace App\Entities;

use App\Entities\Traits\TimestampTrait;
use Carbon\Carbon;
use Carbon\Traits\Date;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\PersistentCollection;
use Illuminate\Database\Eloquent\Model;
use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation As JMS;

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
     * @JMS\Groups({"list"})
     * @JMS\Type("int")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="name",type="string",length=50,nullable=false)
     * @JMS\Groups({"list"})
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="description",type="text",nullable=false)
     * @JMS\Groups({"list"})
     */
    protected $description;

    /**
     * @var \DateTime
     * @ORM\Column(name="close_date",type="datetime",nullable=true)
     * @JMS\Groups({"list"})
     */
    protected $closeDate;

    /**
     * @var integer
     * @ORM\Column(name="num_attempts",type="integer",nullable=true)
     * @JMS\Groups({"list"})
     */
    protected $numAttempts;

    /**
     * @var \DateTime
     * @ORM\Column(name="deleted_at",type="datetime",nullable=true)
     * @JMS\Groups({"list"})
     */
    protected $deletedAt;

    /**
     * @var bool
     * @ORM\Column(name="is_hidden",type="boolean",nullable=true)
     * @JMS\Groups({"list"})
     */
    protected $isHidden;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="QuizSession",mappedBy="quiz")
     * @JMS\Groups({"quiz_sessions"})
     */
    protected $quizSessions;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="QuizQuestion",mappedBy="quiz")
     * @JMS\Groups({"detail"})
     * @JMS\Groups({"quiz_questions"})
     */
    protected $questions;


    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("num_questions")
     * @JMS\Groups({"list","detail"})
     **/
    public function numQuestions(){
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
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return ArrayCollection
     */
    public function getQuestions() : PersistentCollection
    {
        return $this->questions;
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

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->isHidden;
    }

    /**
     * @param bool $isHidden
     */
    public function setIsHidden(bool $isHidden): void
    {
        $this->isHidden = $isHidden;
    }

    public function isOpen($user){

        if (Carbon::today() > $this->closeDate)
            return false;

        // number of user attempts exceeds the max attempts on a quiz
        if ($this->getQuizSessionsByUser($user)->count() >= $this->numAttempts)
            return false;

        return true;
    }


}
