<?php
namespace App\Entity;

use App\Entity\Traits\SoftDeleteTrait;
use App\Entity\Traits\TimestampTrait;
use Carbon\Carbon;
use Carbon\Traits\Date;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * Class Quiz
 * @package App
 *
 * @ORM\Entity(repositoryClass="App\Repository\QuizRepository")
 * @ORM\Table(name="quiz")
 * @ORM\HasLifecycleCallbacks
 *
 */
class Quiz
{
    use TimestampTrait;
    use SoftDeleteTrait;

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
     * One product has many features. This is the inverse side.
     * @ORM\OneToMany(targetEntity="QuizAccess", mappedBy="quiz")
     */
    protected $access;

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
    public function getQuestions() : PersistentCollection
    {
        return $this->questions;
    }

    /**
     *
     * @param $user
     * @return ArrayCollection|\Doctrine\Common\Collections\Collection
     */
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


}
