<?php
namespace App\Entity;

use App\Entity\Traits\TimestampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation As JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
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
 *
 */
class User implements UserInterface
{
    use TimestampTrait;

    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @JMS\Groups({"list","detail","owner"})
     */
    protected $id;

    /**
     * @ORM\Column(type="json")
     * @JMS\Groups({"list","detail","owner"})
     */
    private $roles = [];


    /**
     * @var string
     * @ORM\Column(name="username",type="string",length=100,nullable=false)
     * @JMS\Groups({"user_name","owner"})
     */
    protected $username;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     * @JMS\Groups({"owner"})
     */
    private $lastLogin;

    /**
     * @var string
     * @ORM\Column(name="email",type="string",length=100,nullable=false)
     * @JMS\Groups({"user_email","owner"})
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
     * @JMS\Exclude()
     */
    protected $password;

    /**
     * @var string
     * @ORM\Column(name="azure_id",type="string",length=50,nullable=false)
     * @JMS\Exclude()
     */
    protected $azureId;

    /**
     * @var string
     * @ORM\Column(name="remember_token",type="string",length=100,nullable=false)
     * @JMS\Exclude()
     */
    protected $rememberToken;

    /**
     * Many Groups have Many Users.
     * @ORM\ManyToMany(targetEntity="Classroom", mappedBy="users")
     */
    protected $classes;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="QuizSession",mappedBy="owner")
     * @JMS\Groups({"detail"})
     */
    protected $quizSessions;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     * @var string
     */
    private $plainTextPassword;

    public function __construct($username, array $roles, $email)
    {
        $this->username = $username;
        $this->roles = $roles;
        $this->email = $email;
    }

    /**
     * @param string $rememberToken
     */
    public function setRememberToken(string $rememberToken): void
    {
        $this->rememberToken = $rememberToken;
    }


    /**
     * @param string $azureId
     */
    public function setAzureId(string $azureId): void
    {
        $this->azureId = $azureId;
    }

    public function setEmailVerifiedAt(\DateTime $emailVerifiedAt): void
    {
        $this->emailVerifiedAt = $emailVerifiedAt;
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles): void
    {
        $this->roles = $roles;
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
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
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
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
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
