<?php
namespace App\Entities;

use App\Entities\Traits\TimestampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * Class Quiz
 * @package App
 * @ORM\Entity(repositoryClass="App\Repositories\QuizSessionRepository")
 * @ORM\Table(name="quiz_session")
 * @ORM\HasLifecycleCallbacks
 *
 *
 */
class QuizSession
{
    use TimestampTrait;
    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     *
     * @JMS\Groups({"list","detail"})
     * @JMS\Type("int")
     */
    protected $id;

    /**
     * @var \DateTime
     * @ORM\Column(name="submitted_at",type="datetime",nullable=true)
     * @JMS\Groups({"list"})
     * @JMS\Type("DateTime")
     */
    protected $submittedAt;

    /**
     * @var Quiz
     *
     * @ORM\ManyToOne(targetEntity="Quiz",inversedBy="quizSessions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     * })
     *
     * @JMS\Groups({"list","detail"})
     * @JMS\Type("DateTime")
     */
    protected $quiz;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User",inversedBy="quizSessions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     * })
     * @JMS\Groups({"detail"})
     */
    protected $owner;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="QuizResponse",mappedBy="session")
     *
     * @JMS\Groups({"detail"})
     */
    protected $responses;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setOwner(User $user)
    {
        $this->owner = $user;
        return $this;
    }

    public function getQuiz()
    {
        return $this->quiz;
    }

    public function setQuiz($quiz)
    {
        $this->quiz = $quiz;
        return $this;
    }


    /**
     * @param \DateTime $submittedAt
     */
    public function setSubmittedAt(\DateTime $submittedAt): QuizSession
    {
        $this->submittedAt = $submittedAt;
        return $this;
    }
}
