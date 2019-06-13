<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\TimestampTrait;
use App\Repository\QuestionRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class Quiz.
 *
 * @ORM\Entity(repositoryClass="App\Repository\QuizSessionRepository")
 * @ORM\Table(name="quiz_session")
 * @ORM\HasLifecycleCallbacks
 * @ApiResource(
 *     collectionOperations={
 *          "post",
 *          "get",
 *          "post_start" = {
 *              "method"="POST",
 *              "path"="/quiz_sessions/start",
 *              "controller"=App\Controller\CreateQuizSessionByAccess::class,
 *              "access_control"="has_role('ROLE_USER')",
 *              "swagger_context" = {
 *                  "description" = "Starts a new Quiz Session from access",
 *                  "parameters" = {
 *                      {
 *                      "name" = "body",
 *                      "in" = "body",
 *                      "type" = "object",
*                       "properties" = {"access_id" = { "type" =  "string"},"user_id" = { "type" =  "string"}},
 *                      },
 *                      {
 *                      "name" = "id",
 *                      "in" = "path",
 *                      "required" = "true",
 *                      "type" = "integer"
 *                      },
 *                      {
 *                      "name" = "user_id",
 *                      "in" = "path",
 *                      "required" = "true",
 *                      "type" = "integer"
 *                      }
 *                   },
 *               },
 *          }
 *     }
 * )
 */
class QuizSession
{
    use TimestampTrait;
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     *
     * @Groups({"list","detail"})
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(name="score", type="integer", nullable=true)
     *
     * @Groups({"results"})
     */
    protected $score;

    /**
     * @var int
     *
     * @ORM\Column(name="max_score", type="integer", nullable=true)
     *
     * @Groups({"results"})
     */
    protected $maxScore;

    /**
     * @ORM\ManyToOne(targetEntity="QuizAccess", inversedBy="quizSessions")
     * @ORM\JoinColumn(name="quiz_access_id", referencedColumnName="id",nullable=true)
     */
    protected $quizAccess;

    /**
     * @var array
     *
     * @ORM\Column(name="meta", type="json", nullable=true)
     *
     * @Groups({"meta"})
     *
     */
    protected $meta;

    /**
     * @var \DateTime
     * @ORM\Column(name="submitted_at",type="datetime",nullable=true)
     * @Groups({"list","detail"})
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
     * @Groups({"quiz"})
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
     * @Groups({"session_classroom"})
     */
    protected $classroom;

    /**
     * @var int
     *
     * @ORM\Column(name="current_page", type="integer", nullable=true)
     * @Groups({"list","detail"})
     */
    protected $currentPage = 0;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User",inversedBy="quizSessions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     * })
     * @Groups({"list","detail"})
     */
    protected $owner;

    /**
     * @var PersistentCollection
     * @ORM\OneToMany(targetEntity="QuizResponse",mappedBy="session")
     *
     * @Groups({"results"})
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
    public function setClassroom(Classroom $classroom): QuizSession
    {
        $this->classroom = $classroom;
        return $this;
    }


    /**
     * @return Classroom
     */
    public function getClassroom(): Classroom
    {
        return $this->classroom;
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
    public function getMaxScore(): ?int
    {
        return $this->maxScore;
    }

    /**
     * @param array $meta
     */
    public function setMeta(array $meta): void
    {
        $this->meta = $meta;
    }

    /**
     * @return array
     */
    public function getMeta(): ?array
    {
        return $this->meta;
    }

    /**
     * @return int
     */
    public function getScore(): ?int
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

    /**
     * @return \DateTime
     */
    public function getSubmittedAt(): \DateTime
    {
        return $this->submittedAt;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @param int $current_page
     */
    public function setCurrentPage(int $current_page): void
    {
        $this->currentPage = $current_page;
    }

}
