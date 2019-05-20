<?php
namespace App\Entities;

use App\Entities\Traits\TimestampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Doctrine\ORM\Mapping AS ORM;
use JMS\Serializer\Annotation As JMS;
use Illuminate\Foundation\Auth\User as Authenticatable;
/**
 * Class User
 *
 * @ORM\Entity()
 * @ORM\Table(name="`user`")
 * @ORM\HasLifecycleCallbacks
 *
 */
class User extends Authenticatable
{

    use HasApiTokens, Notifiable;
    use TimestampTrait;

    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @JMS\Groups({"list","detail"})
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="name",type="string",length=100,nullable=false)
     * @JMS\Groups({"user_name"})
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="email",type="string",length=100,nullable=false)
     * @JMS\Groups({"user_email"})
     */
    protected $email;

    /**
     * @var boolean
     * @ORM\Column(name="admin",type="boolean",nullable=false)
     * @JMS\Groups({"user_admin"})
     */
    protected $isAdmin = false;

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
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="QuizSession",mappedBy="owner")
     * @JMS\Groups({"detail"})
     */
    protected $quizSessions;


    public function getId(){
        return $this->id;
    }

    public function isAdmin() : bool {
        return $this->isAdmin;
    }

    /**
     * @param bool $isAdmin
     */
    public function setIsAdmin(bool $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->id;
    }

    public function getKey()
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
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
