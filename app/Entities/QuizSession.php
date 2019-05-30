<?php
namespace App\Entities;

use App\Entities\Traits\TimestampTrait;
use App\Repositories\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\ORM\PersistentCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
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
     * @var integer
     *
     * @ORM\Column(name="score", type="integer", nullable=true)
     *
     * @JMS\Groups({"list","detail"})
     * @JMS\Type("int")
     */
    protected $score;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_score", type="integer", nullable=true)
     *
     * @JMS\Groups({"list","detail"})
     * @JMS\Type("int")
     */
    protected $maxScore;


    /**
     * @var array
     *
     * @ORM\Column(name="meta", type="json", nullable=true)
     *
     * @JMS\Groups({"list","detail"})
     * @JMS\Type("json_array")
     */
    protected $meta;


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
     * @var Classroom
     *
     * @ORM\ManyToOne(targetEntity="Classroom",inversedBy="quizSessions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="classroom_id", referencedColumnName="id")
     * })
     *
     * @JMS\Groups({"detail"})
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
     * @JMS\Groups({"detail"})
     */
    protected $owner;

    /**
     * @var PersistentCollection
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

//
//    public function calculateMaxScore(){
//        $result = 0;
//        Collection::make($this->getQuiz()->getQuestions())->each(function ($q) use (&$result){
//            if($q instanceof MultipleChoiceQuestion){
//                $result++;
//            }
//        });
//        $this->setMaxScore($result);
//        return $result;
//    }
//
//    public function calculateScore(){
//        $responses = Collection::make($this->getResponses())->keyBy(function ($item) {
//            return $item->getQuestion()->getId();
//        });
//        $result = 0;
//        Collection::make($this->getQuiz()->getQuestions())->each(function ($q) use (&$result,$responses) {
//            if ($q instanceof MultipleChoiceQuestion) {
//                if (Arr::exists($responses, $q->getId())) {
//                    /** @var MultipleChoiceResponse $multipleChoiceResponse */
//                    $multipleChoiceResponse = $responses[$q->getId()];
//                    if ($q->getCorrectEntry() === $multipleChoiceResponse->getChoice()) {
//                        $result++;
//                    }
//                }
//            }
//        });
//        $this->setScore($result);
//        return $result;
//    }

    public function getNonResponseQuestions()
    {
        /** @var QuestionRepository $questionRepository */
        $questionRepository = \EntityManager::getRepository(QuizQuestion::class);
        return $questionRepository->filterQuestionsByNotInResponses($this->getQuiz(),$this->getResponses());

    }

}
