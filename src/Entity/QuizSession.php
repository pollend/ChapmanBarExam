<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\TimestampTrait;
use App\Repository\QuestionRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;


/**
 * Class Quiz.
 *
 * @ORM\Entity(repositoryClass="App\Repository\QuizSessionRepository")
 * @ORM\Table(name="quiz_session")
 * @ORM\HasLifecycleCallbacks
 * @ApiResource(
 *     itemOperations={
 *          "get" = {
 *              "access_control"="is_granted('ROLE_ADMIN') | is_granted('ROLE_USER') and object.owner == user"
 *          },
 *          "put" = {
 *              "access_control"="is_granted('ROLE_ADMIN')"
 *          },
 *          "delete" = {
 *              "access_control"="is_granted('ROLE_ADMIN')"
 *          },
 *          "get_tag_breakdown" = {
 *              "method"="GET",
 *              "controller"=App\Controller\GetTagBreakdownAction::class,
 *              "path"="/quiz_sessions/{id}/breakdown",
 *              "normalization_context"={"groups"={"tag:breakdown"}},
 *              "access_control"="is_granted('ROLE_USER') && is_granted('view.report',object)",
 *          },
 *          "post_submit_questions" = {
 *              "method"="POST",
 *              "controller"=App\Controller\CreateQuestionBySessionAndPageAction::class,
 *              "path"="/quiz_sessions/{id}/questions/{page}",
 *              "normalization_context"={"groups"={"quiz_session:get"}},
 *              "access_control"="is_granted('ROLE_USER') && is_granted('edit.questions',object)",
 *          },
 *          "patch_submit_questions" = {
 *              "method"="PUT",
 *              "controller"=App\Controller\CreateQuestionBySessionAndPageAction::class,
 *              "path"="/quiz_sessions/{id}/questions/{page}",
 *              "normalization_context"={"groups"={"quiz_session:get"}},
 *              "access_control"="is_granted('ROLE_USER') && is_granted('edit.questions',object)",
 *          }
 *     },
 *     collectionOperations={
 *          "post" = {
 *              "access_control"="is_granted('ROLE_ADMIN')",
 *           },
 *          "get" = {
 *              "access_control"="is_granted('ROLE_USER')",
 *              "normalization_context"={"groups"={"quiz_session:get", "timestamp"}}
 *           },
 *          "post_start" = {
 *              "method"="POST",
 *              "path"="/quiz_sessions/start",
 *              "controller"=App\Controller\CreateQuizSessionByAccess::class,
 *              "access_control"="is_granted('ROLE_USER')",
 *              "swagger_context" = {
 *                  "description" = "Starts a new Quiz Session from access",
 *                  "parameters" = {
 *                      {
 *                          "name" = "body",
 *                          "in" = "body",
 *                          "type" = "object",
 *                          "properties" = {"access_id" = { "type" =  "string"},"user_id" = { "type" =  "string"}},
 *                      },
 *                      {
 *                          "name" = "id",
 *                          "in" = "path",
 *                          "required" = "true",
 *                          "type" = "integer"
 *                      },
 *                      {
 *                          "name" = "user_id",
 *                          "in" = "path",
 *                          "required" = "true",
 *                          "type" = "integer"
 *                      }
 *                   },
 *               },
 *          }
 *     }
 * )
 * @ApiFilter(OrderFilter::class, properties={"score", "submittedAt"}, arguments={"orderParameterName"="order"})
 * @ApiFilter(ExistsFilter::class, properties={"submittedAt"})
 * @ApiFilter(SearchFilter::class, properties={"id":"exact","owner":"exact","classroom":"exact","quiz":"exact"})
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
     * @Groups({"quiz_session:get","quiz_session:get:report"})
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(name="score", type="integer", nullable=true)
     *
     * @Groups({"quiz_session:get","quiz_session:get:report"})
     */
    protected $score;

    /**
     * @ORM\ManyToOne(targetEntity="QuizAccess", inversedBy="quizSessions")
     * @ORM\JoinColumn(name="quiz_access_id", referencedColumnName="id",nullable=true, onDelete="SET NULL")
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
     * @Groups({"quiz_session:get"})
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
     * @Groups({"quiz_session:get"})
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
     * @Groups({"quiz_session:get"})
     */
    protected $classroom;

    /**
     * @var int
     *
     * @ORM\Column(name="current_page", type="integer", nullable=true)
     * @Groups({"quiz_session:get"})
     */
    protected $currentPage = 0;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User",inversedBy="quizSessions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     * })
     * @Groups({"quiz_session:get:report","quiz_session:get"})
     */
    protected $owner;

    /**
     * @var PersistentCollection
     * @ORM\OneToMany(targetEntity="QuizResponse",mappedBy="session")
     *
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
     * @return mixed
     */
    public function getQuizAccess()
    {
        return $this->quizAccess;
    }

    /**
     * @param mixed $quizAccess
     */
    public function setQuizAccess($quizAccess): void
    {
        $this->quizAccess = $quizAccess;
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
    public function getSubmittedAt(): ?\DateTime
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
