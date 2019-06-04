<?php
namespace App\Entity;

use App\Entity\Traits\TimestampTrait;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\ORM\PersistentCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * Class Quiz
 * @package App
 * @ORM\Entity(repositoryClass="App\Repository\QuizSessionRepository")
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
     * @var integer
     *
     * @ORM\Column(name="score", type="integer", nullable=true)
     *
     * @JMS\Groups({"results"})
     * @JMS\Type("int")
     */
    protected $score;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_score", type="integer", nullable=true)
     *
     * @JMS\Groups({"results"})
     * @JMS\Type("int")
     */
    protected $maxScore;


    /**
     * @var array
     *
     * @ORM\Column(name="meta", type="json", nullable=true)
     *
     * @JMS\Groups({"meta"})
     * @JMS\Type("json_array")
     */
    protected $meta;


    /**
     * @var \DateTime
     * @ORM\Column(name="submitted_at",type="datetime",nullable=true)
     * @JMS\Groups({"list","detail"})
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
     * @JMS\Groups({"quiz"})
     * @JMS\Type("DateTime")
     */
    protected $quiz;


    /**
     * @var Classroom
     *
     * @ORM\ManyToOne(targetEntity="Classroom",inversedBy="quizSessions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="classroom_id", referencedColumnName="id")
     * })
     *
     * @JMS\Groups({"classroom"})
     */
    protected $classroom;


    /**
     * @var integer
     *
     * @ORM\Column(name="current_page", type="integer", nullable=true)
     *
     * @JMS\Groups({"list","detail"})
     * @JMS\Type("int")
     */
    protected $current_page;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User",inversedBy="quizSessions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     * })
     * @JMS\Groups({"list","detail"})
     */
    protected $owner;

    /**
     * @var PersistentCollection
     * @ORM\OneToMany(targetEntity="QuizResponse",mappedBy="session")
     *
     * @JMS\Groups({"results"})
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
     * @param Classroom $classroom
     */
    public function setClassroom(Classroom $classroom): void
    {
        $this->classroom = $classroom;
    }

    /**
     * @return PersistentCollection
     */
    public function getResponses(): PersistentCollection
    {
        return $this->responses;
    }

    /**
     * @return int
     */
    public function getMaxScore(): int
    {
        return $this->maxScore;
    }

    /**
     * @param array  $meta
     */
    public function setMeta(array $meta): void
    {
        $this->meta = $meta;
    }

    /**
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }


    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @param int $score
     */
    public function setScore(int $score): void
    {
        $this->score = $score;
    }

    /**
     * @param int $maxScore
     */
    public function setMaxScore(int $maxScore): void
    {
        $this->maxScore = $maxScore;
    }

    /**
     * @param \DateTime $submittedAt
     */
    public function setSubmittedAt(\DateTime $submittedAt): QuizSession
    {
        $this->submittedAt = $submittedAt;
        return $this;
    }

    public function getNonResponseQuestions()
    {
        /** @var QuestionRepository $questionRepository */
        $questionRepository = \EntityManager::getRepository(QuizQuestion::class);
        return $questionRepository->filterQuestionsByNotInResponses($this->getQuiz(),$this->getResponses());

    }

}
