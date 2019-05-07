<?php
namespace App\Entities;

use App\Entities\Traits\TimestampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Database\Eloquent\Model;
use Doctrine\ORM\Mapping AS ORM;

/**
 * Class Quiz
 * @package App
 * @ORM\Entity(repositoryClass="App\Repositories\QuizSessionRepository")
 * @ORM\Table(name="quiz_session")
 * @ORM\HasLifecycleCallbacks
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
     */
    protected $id;

    /**
     * @var \DateTime
     * @ORM\Column(name="submitted_at",type="datetime",nullable=true)
     */
    protected $submittedAt;

    /**
     * @var Quiz
     *
     * @ORM\ManyToOne(targetEntity="Quiz",inversedBy="quizSessions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     * })
     */
    protected $quiz;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User",inversedBy="quizSessions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     * })
     */
    protected $owner;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="QuizResponse",mappedBy="session")
     */
    protected $responses;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    public function quiz()
    {
        return $this->quiz;
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

}
