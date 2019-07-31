<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Entity\Traits\SoftDeleteTrait;
use App\Entity\Traits\TimestampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClassroomRepository")
 * @ORM\Table(name="classroom")
 * @ORM\HasLifecycleCallbacks
 * @ApiResource(
 * attributes={"pagination_items_per_page"=100},
 * itemOperations={
 *    "get" = {
 *       "normalization_context"={"groups"={"classroom:get","item:classroom:get","timestamp"}},
 *       "access_control"="is_granted('ROLE_USER')"
 *     },
 *     "put" = {
 *         "normalization_context"={"groups"={"classroom:put"}},
 *         "access_control"="is_granted('ROLE_ADMIN')"
 *     },
 *     "get_whitelist" = {
 *          "method"="GET",
 *          "access_control"="is_granted('ROLE_ADMIN')",
 *          "controller"=App\Controller\GetWhitelistByClassroom::class,
 *          "path"= "/classrooms/{id}/whitelist",
 *     },
 *     "put_whitelist" = {
 *          "method"="PUT",
 *          "access_control"="is_granted('ROLE_ADMIN')",
 *          "path"= "/classrooms/{id}/whitelist",
 *          "controller"=App\Controller\PutWhitelistByClassroom::class,
 *     },
 *     "get_score_distribution" = {
 *          "method"="GET",
 *          "access_control"="is_granted('ROLE_ADMIN')",
 *          "path"= "/classrooms/{id}/report/{report_id}/disribution",
 *          "controller"= App\Controller\GetStandardDistributionReport::class,
 *     },
 *     "get_score_standard" = {
 *          "method"="GET",
 *          "access_control"="is_granted('ROLE_ADMIN')",
 *          "path"= "/classrooms/{id}/report/{report_id}/item",
 *          "controller"= App\Controller\GetStandardQuestionReport::class,
 *     },
 *     "delete_user" = {
 *          "method"="DELETE",
 *          "access_control"="is_granted('ROLE_ADMIN')",
 *          "path"= "/classrooms/{id}/user/{user_id}",
 *          "controller"= App\Controller\RemoveUserFromClassroom::class,
 *     }
 * },
 * collectionOperations={
 *     "get" = {
 *          "normalization_context"={"groups"={"classroom:get", "timestamp"}}
 *     },
 *     "post" = {
 *        "access_control"="is_granted('ROLE_ADMIN')",
 *         "validation_groups"={"classroom:post"}
 *     },
 *     "get_by_user" = {
 *          "method"="GET",
 *          "access_control"="is_granted('ROLE_USER')",
 *          "controller"=App\Controller\GetClassesByUser::class,
 *          "path"= "/classrooms/user/{user_id}",
 *          "normalization_context"={"groups"={"classroom:get"}},
 *          "swagger_context" = {
 *                  "description" = "Starts a new Quiz Session from access",
 *                  "parameters" = {}
 *          }
 *     }
 * })
 * @ApiFilter(SearchFilter::class,properties={"id":"exact","users":"exact", "name":"partial", "description":"partial","courseNumber":"partial","users":"exact"})
 * @ApiFilter(DateFilter::class,properties={"createdAt","updatedAt"})
 * @ApiFilter(ExistsFilter::class,properties={"deletedAt"})
 * @ApiFilter(OrderFilter::class,properties= {"name","createdAt","updatedAt"},  arguments={"orderParameterName"="order"})
 */
class Classroom
{
    use TimestampTrait;
    use SoftDeleteTrait;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     *
     * @Groups({"classroom:get","quiz-access:get"})
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="name",type="string",length=50,nullable=false)
     * @Groups({"list","detail"})
     * @Assert\NotBlank(message="Classroom Name Required")
     * @Assert\NotNull()
     * @Groups({"classroom:get","quiz-access:get","classroom:put","classroom:post"})
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="description",type="string",length=500,nullable=true)
     * @Groups({"classroom:get","quiz-access:get","classroom:put","classroom:post"})
     */
    protected $description;

    /**
     * @var string
     * @ORM\Column(name="course_number",type="string",length=50,nullable=true)
     * @Groups({"classroom:get","classroom:put","classroom:post"})
     */
    protected $courseNumber;

    /**
     * @var PersistentCollection
     * @ORM\OneToMany(targetEntity="UserWhitelist",mappedBy="classroom")
     * @Groups({"admin:get"}),
     */
    protected $emailWhitelist;

    /**
     * @var persistentCollection
     *                           Many Users have Many Groups
     * @ORM\ManyToMany(targetEntity="User", inversedBy="classes")
     * @ORM\JoinTable(name="classroom_user")
     * @Groups({"item:classroom:get:ROLE_ADMIN"}),
     */
    protected $users;

    /**
     * @var arrayCollection
     *                      One Product has One Shipment
     * @ORM\OneToMany(targetEntity="QuizAccess",mappedBy="classroom")
     * @Groups({"classroom:get"})
     */
    protected $quizAccess;

    /**
     * @var arrayCollection
     *                      One Product has One Shipment
     * @ORM\OneToMany(targetEntity="QuizSession",mappedBy="classroom")
     * @Groups({"classroom:admin:get"})
     */
    protected $quizSessions;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * @Groups({"classroom:get:ROLE_ADMIN"})
     */
    public function getNumberOfStudents()
    {
        return $this->users->count();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return PersistentCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return PersistentCollection
     */
    public function getEmailWhitelist(): PersistentCollection
    {
        return $this->emailWhitelist;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getCourseNumber(): ?string
    {
        return $this->courseNumber;
    }

    /**
     * @param string $courseNumber
     */
    public function setCourseNumber(string $courseNumber): void
    {
        $this->courseNumber = $courseNumber;
    }

    public function isUserRegistered(User $user)
    {
        return $this->getUsers()->matching(Criteria::create()->where(Criteria::expr()->eq('id', $user->getId())))->count() > 0;
    }

    /**
     * @return mixed
     */
    public function getQuizAccess(): PersistentCollection
    {
        return $this->quizAccess;
    }

    /**
     * @param string $name
     *
     * @return Classroom
     */
    public function setName(string $name): Classroom
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
