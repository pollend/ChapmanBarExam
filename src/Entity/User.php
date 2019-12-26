<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\TimestampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * Class User.
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="`user`")
 * @ORM\HasLifecycleCallbacks
 *
 * @UniqueEntity(
 *     fields="email",
 *     errorPath="email",
 *     message="email already used")
 *
 * @UniqueEntity(
 *     fields="username",
 *     errorPath="username",
 *     message="username already used")
 * @ApiResource(
 *     itemOperations= {
 *          "delete" = {
 *              "access_control"="is_granted('ROLE_ADMIN') |  is_granted('ROLE_USER') and object == user"
 *          },
 *          "put" = {
 *              "access_control"="is_granted('ROLE_ADMIN') |  is_granted('ROLE_USER') and previous_object == user",
 *              "normalization_context"={"groups" = {"user:put"}}
 *          },
 *          "get" = {
 *              "access_control"="is_granted('ROLE_ADMIN') |  is_granted('ROLE_USER') and object == user",
 *              "normalization_context"={"groups" = {"user:get"}}
 *          }
 *     },
 *     collectionOperations={
 *     "post" = {
 *          "access_control"="is_granted('ROLE_ADMIN')",
 *          "normalization_context"={"groups" = {"user:post"}}
 *     },
 *     "get" = {
 *          "access_control"="is_granted('ROLE_ADMIN')",
 *          "normalization_context"={"groups" = {"user:get","timestamp"}}
 *     },
 *     "filter_by_class_exam" = {
 *          "method"="GET",
 *          "access_control"="is_granted('ROLE_ADMIN')",
 *          "normalization_context"={"groups" = {"user:get","timestamp", "quiz_session:get"}},
 *          "controller"=App\Controller\GetUserByClassroomAndExam::class,
 *          "path"= "/users/classroom/{class_id}/quiz/{exam_id}"
 *     },
 *     "get_me" = {
 *         "method" = "GET",
 *         "path"= "/users/me",
 *         "controller"=App\Controller\AuthMe::class,
 *         "access_control"="is_granted('ROLE_USER')",
 *         "normalization_context"={"groups" = {"user:get"}}
 *     }
 * })
 * @ApiFilter(SearchFilter::class,properties={"id":"exact","quizSessions.quiz":"exact","quizSessions.classroom":"exact"})
 */
class User implements UserInterface
{
    use TimestampTrait;

    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @Groups({"user:get","quiz_session:get"})
     */
    protected $id;

    /**
     * @ORM\Column(type="json")
     * @Groups({"user:get"})
     */
    private $roles = [User::ROLE_USER];

    /**
     * @var string
     * @ORM\Column(name="username",type="string",length=100,nullable=false)
     * @Groups({"get","put","post","quiz_session:get"})
     * @Assert\NotBlank()
     * @Assert\Length(min="4",max="100")
     */
    protected $username;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     * @Groups({"user:get"})
     */
    private $lastLogin;

    /**
     * @var string
     * @ORM\Column(name="email",type="string",length=100,nullable=false)
     * @Groups({"user:get","quiz_session:get"})
     * @Assert\Email()
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(name="email_verified_at",type="datetime",nullable=false)
     */
    protected $emailVerifiedAt;

    /**
     * @var string
     * @ORM\Column(name="password",type="string",length=255,nullable=false)
     */
    protected $password;

    /**
     * Many Groups have Many Users.
     *
     * @ORM\ManyToMany(targetEntity="Classroom", mappedBy="users")
     * @Groups({"user:get"})
     */
    protected $classes;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="QuizSession",mappedBy="owner")
     */
    protected $quizSessions;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     *
     * @var string
     */
    private $plainTextPassword;

    public function setEmailVerifiedAt(\DateTime $emailVerifiedAt): void
    {
        $this->emailVerifiedAt = $emailVerifiedAt;
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
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return array_unique($this->roles);
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles): void
    {
        $this->roles = $roles;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return \DateTime
     */
    public function getLastLogin(): ?\DateTime
    {
        return $this->lastLogin;
    }

    /**
     * @return Collection
     */
    public function getQuizSessions(): Collection
    {
        return $this->quizSessions;
    }


    public function getActiveSession()
    {
        return $this->quizSessions->matching(Criteria::create()->where(Criteria::expr()->isNull('submittedAt')));
    }

    /**
     * @return mixed
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        $this->plainTextPassword = '';
    }
}
